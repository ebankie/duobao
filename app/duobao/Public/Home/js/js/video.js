//视频播放器调取
jwplayer("player1").setup({
    stretching: 'fill', //图片延展方式。none：不延展；exactfit：不锁定高宽比填满屏幕；fill：锁定高宽比填满屏幕；默认uniform：锁定高宽比，以黑色填充空白部分。
    autostart:"false", //是否自动播放
    width:"490",
    height:"320",
    repeat:"true",
		wmode: "opaque",
		allowscriptaccess: 'always',
    //backcolor:"#ffccdd", //控制面板和播放清单的背景颜色（默认为白色）
    //frontcolor:"#000", //控制面板和播放清单所有图标和文字颜色
    //lightcolor:"#ffccdd", //鼠标悬停于播放清单时，图标和文字显示颜色
    //screencolor:"#fff", //播放屏幕的背景颜色
    //logo:"none",
    //playlist:"bottom", //播放清单放置位置：无none（默认）、底部bottom、覆盖over、右边right、左边left、顶部
    //playlistsize:"50px", //填写播放清单显示尺寸，默认180pix。
    //icons:"false",//缓冲图标，默认显示true。
    modes: [{
		type: 'flash',
		src: "includes/video/player5.9.swf",
		config: {
			skin: "includes/video/modieus.zip"
		}
		},
		{
		type: 'html5',
		config: {
			skin: "fs40/fs40.xml"
		}
    }]
});