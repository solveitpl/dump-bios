<?php

class HomeController extends BaseController {

	public function showPage($page)
	{
		if (Request::ajax()) {
			# ajax request
			# return no-template
			return View::make($page)->with('nolayout', true);
		} else {
			if (View::exists($page)) {
				return View::make($page)->with('nolayout', false);
			} else {
				return Redirect::to(URL::to('/'));
			}
		}
	}
	public function confirmCookies()
	{
		$cookies = Cookie::make('cookies-agreement', true, 10080);
	}
	public function checkCookies()
	{
		$wantCookies = Cookie::get('cookies-agreement');
		if ($wantCookies) {
			return true;
		} else {
			return false;
		}
	}


}