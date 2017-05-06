<style>
	.mobile-menu{
		background-color: #1b1b1b;
		border: 0px;
    border-right: 2px;
    border-color: white;
    border-style: solid;
	}
	.mobile-menu li a{
		color: white !important;
		font-weight: 500;
		font-size: 17px;
		border-left: 0px;
		border-right: 0px;
		border-top: 0px;
		border-bottom: 1px;
		border-color: #353535;
		border-style: solid;
	}
	#slide-out .social img{
		width: 35px;
		height: auto;
		margin: 3px;
		margin-top: 20px;
		display: inline-block;
	}
	/*TABLET*/
	@media screen and (max-width:992px;) {
	}
	/*MOBILE*/
	@media screen and (max-width:600px) {
	}
	.waves-effect.waves-custom .waves-ripple {
		background-color: rgba(0, 0, 0, 0.7);
	}
</style>


<ul id="slide-out" class="side-nav mobile-menu" >
		<li><a class="waves-effect waves-custom" href="#">Home</a></li>
		<li><a class="waves-effect waves-custom" href="#">Shop</a></li>
		<li><a class="waves-effect waves-custom" href="#">Collections</a></li>
		<li><a class="waves-effect waves-custom" href="#">Contact Us</a></li>
		<div class="social center-align">
			<a href="#" target="_blank"><img src="./images/icons/insta_blanco.png" alt="Instagram Account"></a>
			<a href="#" target="_blank"><img src="./images/icons/fb_blanco.png" alt="Facebook Account"></a>
		</div>

</ul>

<script type="text/javascript">
 //$(".button-collapse").sideNav();
 $('.button-collapse').sideNav({
		 menuWidth: 260, // Default is 300
		 edge: 'left', // Choose the horizontal origin
		 closeOnClick: false, // Closes side-nav on <a> clicks, useful for Angular/Meteor
		 draggable: true // Choose whether you can drag to open on touch screens
	 }
 );

//$('.button-collapse').sideNav('show');
//$('.button-collapse').sideNav('hide');
//$('.button-collapse').sideNav('destroy');
</script>
