<style>
img {
	margin: 0 10px;
	float: right;
}
a:hover {
	text-decoration: underline;
	color:#FFF;
}
a,
p {
	font-weight: normal;
	text-decoration: none;
	color:#FFF;
	font-family: Helvetica-light;
	text-align: center;
	font-size: 17px;
}
.text {
	position: absolute;
	bottom: 5%;
	left: 50%;
	margin-left: -340px;
	width: 700px;
}
img:hover {
	opacity: .5;
}
</style>
<?php
echo $this->Html->link($this->Html->image('tw.png'), 'http://www.twitter.com/family_blog', array('escape' => false, 'target' => '_blank'));
echo $this->Html->link($this->Html->image('fb.png'), 'http://www.facebook.com/app.familyblog', array('escape' => false, 'target' => '_blank'));
?>
<div class="text">
<p>Family Blog es tu APP móvil para mamás y papás. Se trata de una revista digital que reúne los mejores blogs del sector materno-infantil. Desde la herramienta puedes seguir tus blogs preferidos y compartir la información que quieras en redes sociales <br /><br />
<?php //¿Quieres que tu blog esté en Family Blog? Escríbenos a <a href="mailto:colabora@familyblog.es">colabora@familyblog.es</a></p> ?>
¿Quieres que tu blog esté en Family Blog? Escríbenos a <a href="m&#97;ilt&#111;&#58;c&#111;l&#97;b&#111;r&#97;&#64;fami&#108;yblo&#103;.&#101;s">c&#111;l&#97;b&#111;r&#97;&#64;fami&#108;yblo&#103;.&#101;s</a></p>
<br />
<?php
echo $this->Html->link($this->Html->image('apple.png'), 'https://itunes.apple.com/es/app/family-blog-tu-revista-bloggers/id577736520?mt=8', array('escape' => false, 'target' => '_blank'));
echo $this->Html->link($this->Html->image('android.png'), 'https://play.google.com/store/apps/details?id=net.artvisual.bloggers', array('escape' => false, 'target' => '_blank'));
?>
</div>
