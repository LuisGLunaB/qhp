<style>
	footer{

	}

	#tel{

	}

	#contact-us{
		position: fixed;
		display: block;
		bottom: 80px;
		right: 20px;
		width: 45px;
		height: 45px;
		background-color: rgba(0,0,0,0.4);
	}

	.copyright{
		font-size: 10px;
	}

	.copyright a{
		color: inherit;
	}

	/*TABLET*/
	@media screen and (max-width:992px) {
		#contact-us{
			bottom: 10px;
			left: 10px;
		}
	}
	/*MOBILE*/
	@media screen and (max-width:200px) {
	}

</style>


<footer>
	<a id="contact-us" href="#">
		<span style="margin-top:-20px;margin-left: -7px; position: fixed; display: block; font-size:11px;color: #999999; font-weight: 700;">Contact Us</span>
		<img class="responsive-img pad-10" src="<?php echo $ROOT; ?>/images/icons/email.png" alt="Contact us button">
	</a>

  <!-- <div class="row">
  	Pie de página
  </div>

  <div class="row copyright txc pav5">
  	&nbsp;
  </div> -->

	<!-- <a href="tel:XXXXXXXX">
		<img id="tel" alt="Llamar" src="<?php echo $ROOT;?>/images/icons/llamar.png" />
	</a> -->
</footer>
