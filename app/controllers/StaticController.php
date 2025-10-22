<?php
class StaticController {
    public function about() {
        render('static/about');
    }

    public function policies() {
        render('static/policies');
    }

    public function adoptions() {
        render('static/adoptions');
    }

    public function notFound() {
        http_response_code(404);
        render('static/404');
    }
}