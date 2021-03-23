FFMPEG is the swiss-army knife for video/audio editing from commandline. But there is no unified frontend for the dizzying number of commands <BR >
![frontend for ffmpeg](https://github.com/wilwad/frontend-for-ffmpeg/blob/main/ffmpeg2.png?raw=true)
How do we define parameters to create valid commands for ffmpeg in HTML <BR >
 
Notes: <BR >
%dir% is "ffmpegresults/"  <BR >
%random% is a random file number generated internally <BR >
%format% is the extension parsed from uploaded file (always used when 'source' is selected)  <BR >
 
ffmpeg actions and their required parameters <BR >  <BR >
cmd      : your ffmpeg command definition with key value pair in %key% %value% format <BR >
controls : html input controls to be rendered on the form <BR >
            control.name : unique name for the control, it will be replaced in cmd with matching %name% <BR >
            control.caption : label for form control <BR >
            control.type : file | text | number | select  <BR >
            control.source : when control.type = select, this is a text file inside "ffmpeglists/" for common values e.g. bitrates (64k, 128k,etc), libs (libx264, libx265, etc) <BR >
              control.select_exclude (array) : when control.source is set - ignore specific values loaded from file e.g. 'select_exclude'=>['source', 'mp3'] <BR >
              control.default : the default value for to be selected (when control.type=select) or displayed (when control.type = text or number) <BR >
              control.required : whether the value is required. Infact all values are required since ffmpeg will fail if there's %etc% in the cmd string <BR >
              control.accept : (when control.type=file, limit what the FILE dialog shows e.g. 'video/*', 'image/*', '.jpg,.jpeg,.gif,.png') <BR >
              control.maxlength : set max length for text input, or '0' to ignore maxlength <BR >
              
all other keys are to be ignored/removed (e.g. small, placeholder, callback) - this control definition is borrowed from one of my old projects <BR >
 
here's an ffmpeg command to create a video slideshow from images <BR > <BR >
 
ffmpeg -r 1/5 -i img%03d.png -c:v libx264 -vf fps=25 -pix_fmt yuv420p out.mp4 <BR > <BR >
 
becomes: <BR >
ffmpeg -r %rate% -i %pattern% -c:v %lib% -vf fps=%fps% %random%.%format% <BR >
 
Well this one is complicated because it uses sequentially named-images, which we need to zip since we can only upload 1 file <BR >
so our controls definition looks like this: <BR >
 
 'title'   =>"Create a video slideshow from images", <BR >
   'cmd'     =>"ffmpeg -r %rate% -i %pattern% -c:v %lib% -vf fps=%fps% %random%.%format%", <BR >
 'controls'=>[ <BR >
                ['name'=>'filename','caption'=>'Zip file', 'type'=>'file', 'accept'=>'.zip', 'required'=>true], <BR >
 				  ['name'=>'rate',     'caption'=>'Frame rate','default'=>'1/5', 'type'=>'text', 'required'=>true], 	  <BR >
 				  ['name'=>'pattern', 'caption'=>'Pattern','default'=>'img%03d.png', 'type'=>'text', 'required'=>true], <BR >
 				  ['name'=>'lib',     'caption'=>'Library', 'type'=>'select', 'source'=>'ffmpeglists/libs.txt','default'=>'libx264', 'required'=>true],  <BR >
 				  ['name'=>'fps',     'caption'=>'Frames per second','default'=>'25', 'type'=>'number', 'required'=>true], 				   <BR >
             ] <BR >

