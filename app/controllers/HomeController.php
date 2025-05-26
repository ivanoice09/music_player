<?php
class HomeController extends BaseController
{
    private $musicModel;

    public function __construct()
    {
        $this->musicModel = new Music();
    }

    public function index()
    {
        // Get some featured tracks for the home page
        $featuredTracks = $this->musicModel->getPopularTracks(36);

        $data = [
            'featuredTracks' => $featuredTracks
        ];

        $this->view('home/index', $data);
    }

    public function main()
    {
        if ($this->isAjaxRequest()) {
            // For AJAX requests, return just the content
            $featuredTracks = $this->musicModel->getPopularTracks(36);
            $data = [
                'featuredTracks' => $featuredTracks,
                'content' => '' // This will be filled by the view
            ];
            $this->view('home/index', $data);
        } else {
            // For full page loads
            $this->index();
        }
    }
}
