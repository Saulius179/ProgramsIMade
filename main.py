import cv2
#import matplotlib.pyplot as plt
import imutils
import numpy as np
import math
#import keras_ocr
from PIL import Image
#from pickle import NONE

class Digit_and_x:
    def __init__(self, gDigit, gX):
        self.x= gX 
        self.Digit=gDigit
    def print_all_class_attributes(self):
        prntstring=""
        prntstring+= " Digit : "+ str(self.Digit) + "   x coordinate : "+ str(self.x)
        print(prntstring)

def PIL2CV2Rotate(gimage, gangle):
    # IMPORT IMAGE TO PILLOW FORMAT SO WE CAN APPLY ROTATION 
    pil_image =  Image.fromarray(cv2.cvtColor(gimage, cv2.COLOR_BGR2RGB)) 
    #ROTATE
    pil_image_rotated = pil_image.rotate((gangle))
    # CONVERT PIL IMAGE BACK TO OPENCV
    roi= np.array(pil_image_rotated)
    roi = roi[:, :, ::-1].copy()
    return roi



def Filter_Out_Rbg_Color(given_image, color_rbg_lower_range, color_rbg_upper_range):
    # CHANGE IMG FORMAT TO RBG
    rbg = cv2.cvtColor(given_image, cv2.COLOR_BGR2RGB)
    mask= cv2.inRange(rbg, color_rbg_lower_range, color_rbg_upper_range)
    return mask



def Paste_Image_At_Center_Background(gimage, gbackground):
    # PASTE THRESHOLDED INTO BLACK 
    pil_thresh =  Image.fromarray(cv2.cvtColor(gimage, cv2.COLOR_BGR2RGB)) 
    thresholded_w, thresholded_h =pil_thresh.size
    pil_background = Image.fromarray(cv2.cvtColor(gbackground, cv2.COLOR_BGR2RGB))
    background_w, background_h = pil_background.size
    offset = ((background_w - thresholded_w)//2, (background_h-thresholded_h)//2)
    pil_background.paste(pil_thresh, offset)
    background_thresh = np.array(pil_background)
    background_thresh=background_thresh[:,:,::-1].copy()
    return background_thresh

def Recognize7Segment(gimage):

    List_of_recognized_digits = []
    
    DIGITS_LOOKUP = {
        (1, 1, 1, 0, 1, 1, 1): 0,
        (0, 0, 1, 0, 0, 1, 0): 1,
        (1, 0, 1, 1, 1, 1, 0): 2,
        (1, 0, 1, 1, 0, 1, 1): 3,
        (0, 1, 1, 1, 0, 1, 0): 4,
        (1, 1, 0, 1, 0, 1, 1): 5,
        (1, 1, 0, 1, 1, 1, 1): 6,
        (1, 0, 1, 0, 0, 1, 0): 7,
        (1, 1, 1, 1, 1, 1, 1): 8,
        (1, 1, 1, 1, 0, 1, 1): 9
    }
    
    
    # READ IMAGE FOR IN CV2
    img_color = gimage
    
    # MAKE IT BLACK/WHITE
    img_gray= cv2.cvtColor(img_color, cv2.COLOR_BGR2GRAY)
    
    # BLUR FOR REMOVAL OF NOISE 
    img_blurred = cv2.GaussianBlur(img_color, (7,7),0)
    
    mask = Filter_Out_Rbg_Color( img_blurred , np.array([0,170,235]), np.array([50,255,255]))
    
   
    # FIND/SORT CONTOURS BY AREA
    cnts = cv2.findContours(mask.copy(), cv2.RETR_EXTERNAL,cv2.CHAIN_APPROX_SIMPLE)
    cnts = imutils.grab_contours(cnts)
    cnts = sorted(cnts, key=cv2.contourArea, reverse=True)[:15]
    
    #DEFINE CONTOUR VECTOR COORDINATES
    greenx=0
    greeny=0
    centerx=0
    centery=0
    
    # DRAW BOUNDARY OF CONTOUR
    approx = cv2.approxPolyDP(cnts[0], 0.009 * cv2.arcLength(cnts[0],True),True)
    #cv2.drawContours(img_color, [approx],0,(0,0,255),5)
    # Find EDGE COORDINATED OF CONTOUR
    n= approx.ravel()
    i=0
    for j in n:
        if(i%2 ==0):
            y=n[i]
            x=n[i+1]
            
            if(i==2):
                greenx=x
                greeny=y
            elif(i==4):
                #plt.plot(y,x, marker="o", color="red") # POINT 3 (CENTER)
                centerx=x
                centery=y
                pass
        i=i+1   
            
    # FIND 3 POINTS FOR ANGLE CALCULATION-----------------------------------------------------------------------------------------
   
    # CROP OUT BIGGEST CONTOUR (SHOULD BE THE LED SCREEN RECTANGLE)
    x, y, w, h = cv2.boundingRect(cnts[0])
    #plt.plot(greeny,greenx, marker="o", color = "green") # POINT 1
    p1= [greenx,greeny]
    #plt.plot(10,centerx, marker="o", color="yellow") # POINT 2
    p2= [centerx,10]
    #plt.plot(centery,centerx, marker="o", color="blue") # POINT 3 (CENTER)
    p3= [centerx,centery]
    
    # CALCULATE ANGLE
    gradient1= (p1[1]-p3[1]) / (p1[0]-p3[0] +1e-10 )
    gradient2 = (p2[1]-p3[1]) / (p2[0]-p3[0]+ 1e-10)
    angR=math.atan( (gradient2-gradient1)/(1+(gradient2*gradient1)) )
    angD= math.degrees(angR)
    
    # ROTATE
    roi = PIL2CV2Rotate(img_blurred, (angD))
    mask= PIL2CV2Rotate(mask,angD)
    mask = cv2.cvtColor(mask, cv2.COLOR_BGR2GRAY)
    img_blurred= PIL2CV2Rotate(img_blurred, (angD))
    
    # FIND/SORT CONTOURS BY AREA (AGAIN)
    cnts = cv2.findContours(mask.copy(), cv2.RETR_EXTERNAL,cv2.CHAIN_APPROX_SIMPLE)
    cnts = imutils.grab_contours(cnts)
    cnts = sorted(cnts, key=cv2.contourArea, reverse=True)[:15]
    # CROP OUT BIGGEST CONTOUR (SHOULD BE THE LED SCREEN RECTANGLE) (AGAIN)
    x, y, w, h = cv2.boundingRect(cnts[0])
    #CROP FUNCTION ITSELF
    roi = img_blurred[y : y + h, x : x+ w]
    roi = PIL2CV2Rotate(roi, (2))
    
    # RESIZE TO GET CONSTANT SIZE = SCREEN WILL ALWAYS HAVE SAME PROPORTIONS RELATIVE TO NUMBERS IN IT
    roi = cv2.resize(roi, (300, 100)) 
    
    # ZOOM IN 
    roiWidth, roiHeight  = Image.fromarray(cv2.cvtColor(roi, cv2.COLOR_BGR2RGB)).size
    roi= roi[10: int( roiHeight*0.93) , 5: int(roiWidth*0.95)]
    
    # ADAPTIVE THRESHOLD OPERATION
    thresh = cv2.adaptiveThreshold( cv2.cvtColor(roi, cv2.COLOR_BGR2GRAY) , 255,cv2.ADAPTIVE_THRESH_MEAN_C, cv2.THRESH_BINARY_INV, 21, 5)
    
    # GENERATE BLACK IMAGE BINARY
    black_img= np.random.rand(1000,1000)
    black_img[black_img <= 0.0] = 0
    black_img= np.uint8(black_img)
    
    
    # PASTE THRESHOLDED INTO BLACK 
    background_thresh=Paste_Image_At_Center_Background(thresh, black_img)
    
    # DILATE + ERODE UNTIL THERE ARE NO MORE GAPS -------------------------------------------------------------------------
    kernel = cv2.getStructuringElement(cv2.MORPH_RECT, (1,3))
    dilated = cv2.dilate(background_thresh, kernel, iterations =1)
    #kernel = cv2.getStructuringElement(cv2.MORPH_RECT, (2,1))
    #eroded = cv2.erode(background_thresh, kernel, iterations = 1)
    kernel = cv2.getStructuringElement(cv2.MORPH_RECT, (1,12))
    closing = cv2.morphologyEx(dilated, cv2.MORPH_CLOSE, kernel)
    
    # CONVERT TO BINARY AGAIN
    ret,binary_again = cv2.threshold(cv2.cvtColor(closing, cv2.COLOR_BGR2GRAY), 170, 255, cv2.THRESH_BINARY)
    dilated = binary_again
    
    # FIND/SORT CONTOURS BY AREA (OF DIGITS)
    cnts2 = cv2.findContours(dilated, cv2.RETR_EXTERNAL,cv2.CHAIN_APPROX_SIMPLE)
    cnts2 = imutils.grab_contours(cnts2)
    cnts2 = sorted(cnts2, key=cv2.contourArea, reverse=True)[:9]
    
    # LOOP THROUGH THE 7 DIGITS ON THE SCREEN
    contour_area_iterator = -1
    while contour_area_iterator < 6:
        contour_area_iterator= contour_area_iterator+1
        
        # CROP OUT BIGGEST CONTOUR
        x, y, w, h = cv2.boundingRect(cnts2[contour_area_iterator])
        roi = dilated[y : y + h, x : x+ w]
        
        #--------------------------------------------------------- MANUAL 7 SEGMENT RECOGNITION METHOD
        
        # compute width and height of each segment
        (roiH, roiW) = roi.shape
        (dW, dH) = (int(roiW * 0.33), int(roiH * 0.15)) # 15 % of picture - segment height. 25% of picture - width
        dHC = int(roiH*0.05)
        
        # COORDINATES OF  IS SEGMENT ON CHECK AREA
        segments = [
            ((0, 0), (w, dH)),    # top
            ((0, 0), (dW, h // 2)),    # top-left
            ((w - dW, 0), (w, h // 2)),    # top-right
            (( int(w-w*0.8), (h // 2) - dHC) , (int(w*0.8), (h // 2) + dHC)), # center
            ((0, h // 2), (dW, h)),    # bottom-left
            ((w - dW, h // 2), (w, h)),    # bottom-right
            ((0, h - dH), (w, h))    # bottom
            
            ]
        
        on = [0]* len(segments) # LIST that saves the state of all segments [0,0,0,0,0,0]
        
        # LOOP SEGMENT AREAS / FIND OUT IF THEY ARE (ON/OFF)
        for (i, ((xA, yA), (xB, yB))) in enumerate(segments):
            segROI = roi[yA:yB, xA:xB]
            total = cv2.countNonZero(segROI)
            area = (xB - xA) * (yB - yA)
            
            
            # if the total number of non-zero pixels is greater than
            # 50% of the area, mark the segment as "on"
            if total / (float(area)+1e-10) > 0.5:
                on[i]= 1
                cv2.rectangle(roi,pt1=(xA,yA),pt2=(xB,yB),color=(55,255,255),thickness=1)
                
            else:
                cv2.rectangle(roi,pt1=(xA,yA),pt2=(xB,yB),color=(255,0,175),thickness=1)
                pass
                
            printimg = cv2.resize(roi, (0, 0), fx=5, fy=5) 
            #printimg = cv2.resize(roi, (500, 500))
        
        digit= 'x'
        if  tuple(on) in DIGITS_LOOKUP:
            digit = DIGITS_LOOKUP[tuple(on)]
            #printimg = cv2.resize(roi, (0, 0), fx=5, fy=5)
            #cv2.imshow('digit found',  printimg)
            #cv2.waitKey(0)
            
        else:
            printimg = cv2.resize(roi, (0, 0), fx=5, fy=5)
            cv2.imshow('digit not found',  printimg)
            cv2.waitKey(0)
            
        List_of_recognized_digits.append(Digit_and_x(digit, x)  )
      
    List_of_recognized_digits= sorted(List_of_recognized_digits, key=lambda x: x.x, reverse=False)
    final_num_printstrng = ""
    for digit in List_of_recognized_digits:
        final_num_printstrng+= str(digit.Digit)
    return final_num_printstrng
      
# CONVERT VIDEO TO FRAMES
cap = cv2.VideoCapture(r'C:\\Users\\Saulius\\Documents\\Rotated.mp4')
cap.set(1,7*30)
ret, frame = cap.read() 

#cv2.imshow('window_name', frame)

#image = cv2.imread("C:\\Users\\Saulius\\Documents\\IMG_20220224_180928.jpg")
Recognize7Segment(frame)


# TEXT ON VIDEO
while(True):
    ret, frame = cap.read()
    font = cv2.QT_FONT_NORMAL
    color= (173,255,47) #(50,20,230)
    cv2.putText(frame, "Detected number: [ "+Recognize7Segment(frame)+" ]"  , (20,150),font,2.3, color, 4,cv2.LINE_8 )
    cv2.imshow("Edited video", cv2.resize(frame, (0,0), fx=0.5, fy=0.5) )
    if(cv2.waitKey(22) % 0xFF == ord("q")):
        break
    
cap.release()
cv2.destroyAllWindows()




