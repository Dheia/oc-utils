<?php namespace Waka\Utils\WakaRules\Asks;

use Waka\Utils\Classes\Rules\AskBase;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use ApplicationException;

class ImageAsk extends AskBase
{
    protected $tableDefinitions = [];

    /**
     * Returns information about this event, including name and description.
     */
    public function askDetails()
    {
        return [
            'name'        => 'Une image dans le répertoire média',
            'description' => 'Choisissez Une image dans le répertoire média',
            'icon'        => 'icon-picture-o',
            'word_type' => 'IMG',
        ];
    }

    public function getText()
    {
        $hostObj = $this->host;
        $url = $hostObj->config_data['image'] ?? null;
        if($url) {
            return "image : ".$url;
        }
        return parent::getText();

    }

    public function resolve($modelSrc, $context = 'twig', $dataForTwig = []) {
        return 'yo';
    }
}
