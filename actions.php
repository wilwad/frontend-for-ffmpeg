$actions = 
[
	'deshake'=>"ffmpeg -i input.mov -vf deshake output.mov",
	'fadeinout-video'=>"ffmpeg -i input.mp4 -vf "fade=t=in:st=0:d=5,fade=t=out:st=10:d=5" -c:a copy output.mp4",
	'fadeinout-audio'=>"ffmpeg -i input.mp4 -af "afade=t=in:st=0:d=5" -c:v copy output.mp4",
	'extractaudio'=>"ffmpeg -i input.mp4 -vn -acodec copy output.mp3",
	'videoremoveaudio'=>"ffmpeg -i videoWithAudio.mp4 -c:v copy -an videoWithoutAudio.mp4",
];
