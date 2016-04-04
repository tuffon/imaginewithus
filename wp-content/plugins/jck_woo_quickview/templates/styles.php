<style>

/* Add to Cart */

	#jckqv .quantity {
		display: <?php echo ($this->settings['content_showqty'] == 1) ? 'inline' : 'none !important'; ?>;
	}

	<?php if($this->settings['content_themebtn'] != 1){ ?>

		#jckqv .button {
			background: <?php echo $this->settings['content_btncolour']; ?>;
			color: <?php echo $this->settings['content_btntextcolour']; ?>;
		}

			#jckqv .button:hover {
				background: <?php echo $this->settings['content_btnhovcolour']; ?>;
				color: <?php echo $this->settings['content_btntexthovcolour']; ?>;
			}

	<?php } ?>

</style>