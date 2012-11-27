function ads(args) {

	args.target = args.target || '_blank';
	args.width = args.width || 800;
	args.height = args.height || 600;
	args.timer = (args.timer  * 1000)|| 5000;
	args.delay = (args.delay * 1000) || 0;

	if (!args.img) {
		console.log('El parámetro no es correcto');
		return;
	}
	
	var prefix = 'css' + Math.round(Math.random() * 10000) + 1;

	var img = document.createElement('img');
	img.src = args.img;

	if (args.url) {
		var a = document.createElement('a');
		a.href = args.url;
		a.appendChild(img);

		if (args.target) {
			a.setAttribute('target', args.target);
		}
	}

	var head = document.getElementsByTagName('head')[0];

	var css = '';
	var style = null;
	img.onload = function() {
		css += '.' + prefix + '_overlay {z-index:997; width:100%; height:100%; position:absolute; top: 0px; left: 0px; background:#000; opacity:0.2;}';
		css += '.' + prefix + '_div {z-index:999; position:absolute; top: 50%; margin-top:-' + img.height / 2 + 'px; left: 50%; margin-left:-' + (img.width / 2) + 'px; max-width: ' + args.width + 'px; max-height: ' + args.width + 'px;}';
		css += '.' + prefix + '_bg {z-index:998; position:absolute; top: 50%; margin-top:-' + args.height / 2 + 'px; border:solid 5px #000; left:50%; margin-left:-' + args.width / 2 + 'px; width:' + args.width + 'px; height:' + args.height + 'px; background:#666; opacity:0.7;}';
		css += '.' + prefix + '_close {z-index:999; cursor:pointer; display:block; text-decoration:none; color:#FFF; position:absolute; top: 5px; right:10px;}';

		style = document.createElement('style');
		style.type = 'text/css';
		if(style.styleSheet){
			style.styleSheet.cssText = css;
		} else {
			style.appendChild(document.createTextNode(css));
		}

		head.appendChild(style);
	}

	var overlay = document.createElement('div');
	//overlay.setAttribute('class', prefix + '_overlay');
	overlay.className = prefix + '_overlay';

	var bg = document.createElement('div');
	//bg.setAttribute('class', prefix + '_bg');
	bg.className = prefix + '_bg';

	var div = document.createElement('div');
	//div.setAttribute('class', prefix + '_div');
	div.className = prefix + '_div';

	if (args.url) {
		div.appendChild(a);
	} else {
		div.appendChild(img);
	}

	setTimeout(function() {
		document.body.appendChild(overlay);
		document.body.appendChild(bg);
		document.body.appendChild(div);
	}, args.delay);

	var closeButton = document.createElement('a');
	//closeButton.setAttribute('class', prefix + '_close');
	closeButton.className = prefix + '_close';
	closeButton.innerHTML = 'X';
	bg.appendChild(closeButton);

	setTimeout(function() {
		close();
	}, args.timer + args.delay);

	closeButton.onclick = function() {
		close();
	}

	function close() {
		document.body.removeChild(overlay);
		document.body.removeChild(div);
		document.body.removeChild(bg);
		head.removeChild(style);
	}

}
