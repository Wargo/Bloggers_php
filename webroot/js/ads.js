function ads(args) {

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

	img.onload = function() {
		var css = '.' + prefix + '_div {z-index:999; position:absolute; top: 50%; margin-top:-' + img.height / 2 + 'px; left: 50%; margin-left:-' + (img.width / 2) + 'px; max-width: ' + args.width + 'px; max-height: ' + args.width + 'px;}';
		css += '.' + prefix + '_bg {z-index:998; position:absolute; top: 50%; margin-top:-' + args.height / 2 + 'px; border:solid 5px #000; left:50%; margin-left:-' + args.width / 2 + 'px; width:' + args.width + 'px; height:' + args.height + 'px; background:#999; opacity:0.7;}';

		var style = document.createElement('style');
		style.type = 'text/css';
		if(style.styleSheet){
			style.styleSheet.cssText = css;
		} else {
			style.appendChild(document.createTextNode(css));
		}

		head.appendChild(style);
	}

	var bg = document.createElement('div');
	bg.setAttribute('class', prefix + '_bg');

	document.body.appendChild(bg);

	var div = document.createElement('div');
	div.setAttribute('class', prefix + '_div');

	if (args.url) {
		div.appendChild(a);
	} else {
		div.appendChild(img);
	}

	document.body.appendChild(div);

}
