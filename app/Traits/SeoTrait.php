<?php
namespace App\Traits;

use App\Models\SeoDetails;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;

trait SeoTrait{
    //Set Seo
    public function setSeo($seoData){
        SeoDetails::create($seoData);
    }
    //Get Seo
    public function getSeo($record){
        $data = $record->seo;
        if (!empty($data->details)) {
            //Seo Meta
            $seoMeta = $data->details['SEOMeta'];
            SEOMeta::setTitle($seoMeta['title']);
            SEOMeta::setDescription($seoMeta['description']);
            SEOMeta::setCanonical($seoMeta['canonical']);
            SEOMeta::addMeta($seoMeta['addMeta']['article:published_time']);
            SEOMeta::addMeta($seoMeta['addMeta']['article:section']);
            $keywords = implode(',',$seoMeta['keywords']);
            SEOMeta::addKeyword([$keywords]);
            //Twitter Card
            $twitterCard = $data->details['TwitterCard'];
            TwitterCard::setTitle($twitterCard['title']);
            TwitterCard::setSite($twitterCard['site']);
            //Open Graph
            $openGraph = $data->details['OpenGraph'];
            OpenGraph::setDescription($openGraph['description']);
            OpenGraph::setTitle($openGraph['title']);
            OpenGraph::setUrl($openGraph['url']);
            $openGraphProperty = $data->details['OpenGraph']['properties'];
            OpenGraph::addProperty('type',$openGraphProperty['type']);
            OpenGraph::addProperty('locale',$openGraphProperty['locale']);
            OpenGraph::addImage($openGraph['image']);
            //Json Ld
            $jsonLd = $data->details['JsonLd'];
            JsonLd::setTitle($jsonLd['title']);
            JsonLd::setDescription($jsonLd['description']);
            JsonLd::setType($jsonLd['type']);
            JsonLd::addImage($jsonLd['image']);
        }
    }
}
