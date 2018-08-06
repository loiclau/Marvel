<?php

namespace Template;

use Template\Entity\Quote;
use Template\Entity\Template;
use Template\Context\ApplicationContext;
use Template\Repository\QuoteRepository;
use Template\Repository\SiteRepository;
use Template\Repository\DestinationRepository;

class TemplateManager
{


    private $applicationContext;
    private $oQuote;
    private $oSite;
    private $oDestination;

    private $quoteRepository;
    private $destinationRepository;
    private $siteRepository;

    /**
     * TemplateManager constructor.
     */
    public function __construct()
    {
        $this->applicationContext = ApplicationContext::getInstance();
    }

    /**
     * @param Quote $quote
     */
    public function initQuote(Quote $quote)
    {
        $this->oQuote = QuoteRepository::getInstance();
        $this->oSite = SiteRepository::getInstance();
        $this->oDestination = DestinationRepository::getInstance();

        $this->quoteRepository = $this->oQuote->getById($quote->id);
        $this->siteRepository = $this->oSite->getById($quote->siteId);
        $this->destinationRepository = $this->oDestination->getById($quote->destinationId);
    }

    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }



    private function computeText($text, array $data)
    {
        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote)
        {
            $this->initQuote($quote);

            $containsSummaryHtml = strpos($text, '[quote:summary_html]');
            $containsSummary     = strpos($text, '[quote:summary]');

            if ($containsSummaryHtml !== false || $containsSummary !== false) {
                if ($containsSummaryHtml !== false) {
                    $text = str_replace(
                        '[quote:summary_html]',
                        Quote::renderHtml($this->quoteRepository),
                        $text
                    );
                }
                if ($containsSummary !== false) {
                    $text = str_replace(
                        '[quote:summary]',
                        Quote::renderText($this->quoteRepository),
                        $text
                    );
                }
            }

            (strpos($text, '[quote:destination_name]') !== false) and $text = str_replace('[quote:destination_name]',$this->destinationRepository->countryName,$text);
        }

        if (!empty($this->destinationRepository))
            $text = str_replace('[quote:destination_link]', $this->siteRepository->url . '/' . $this->destinationRepository->countryName . '/quote/' . $this->quoteRepository->id, $text);
        else
            $text = str_replace('[quote:destination_link]', '', $text);

        /*
         * USER
         * [user:*]
         */
        $_user  = (isset($data['user'])  and ($data['user']  instanceof User))  ? $data['user']  : $this->applicationContext->getCurrentUser();
        if($_user) {
            (strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]'       , ucfirst(mb_strtolower($_user->firstname)), $text);
        }

        return $text;
    }
}
