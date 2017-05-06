<style>
	header{
		background-color: white;
		position: fixed;
		top: 0px;
		left: 0px;
		width: 100%;
		height: auto;
		z-index: 100;
	}

	#logo{
		max-width: 310px;
		min-width: 200px;
		height: auto;
	}

	#display-menu-button{
		position: fixed;
		width: 22px;
		height: auto;
		top: 35px;
		right: 40px;
		z-index: 101;
	}

	.menu{}

	.menu a{
		text-decoration: none;
	}

	/*TABLET*/
	@media screen and (max-width:992px) {
		#logo{
			max-width: 35%;
		}
		#display-menu-button{
			top: 28px;
			right: 25px;
		}
	}
	/*MOBILE*/
	@media screen and (max-width:600px) {
		#display-menu-button{
			top: 20px;
			right: 20px;
		}
	}
</style>

<header class="row">
		<div class="col m12 l6 left-align">
			<a href="<?php echo $ROOT; ?>/index.php">
				<img id="logo" class="pad-h-3 pad-v-10" src="<?php echo $ROOT; ?>/images/logo.png" alt="Logo Dr. Hector Gonzalez Miramontes">
			</a>
		</div>
		<div class="col  m0 l6 right-align" > &nbsp; </div>

	<!-- <a href="javascript:">
		<img id="display-menu-button" data-activates="slide-out" class="button-collapse" src="<?php echo $ROOT; ?>/images/icons/menu-elegant.png" alt="Display Menu Button">
	</a> -->
</header>

<?php include_once("$ROOT/html/mobile-menu.php"); ?>

<div id="header-space" style="height: 0px;display: block;"> &nbsp; </div>
