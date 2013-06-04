<?php

class captchaAction extends Action{//frontendAction {

    public function _empty() {
		import("ORG.Util.Image");
		//print_r("1");
		Image::buildImageVerify(4, 1, 'png', '50', '24', 'captcha');
    }
}