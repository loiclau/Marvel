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

    /**
     * @param Template $tpl
     * @param array $data
     * @return Template
     */
    public function getTemplateComputed(Template $tpl, array $data): Template
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    /**
     * @param $text
     * @return mixed
     */
    private function computeSummary($text): string
    {
        $containsSummaryHtml = strpos($text, '[quote:summary_html]');
        $containsSummary = strpos($text, '[quote:summary]');

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
        return $text;
    }

    /**
     * @param $text
     * @return mixed
     */
    private function computeDestination($text): string
    {
        if (strpos($text, '[quote:destination_name]') !== false) {
            $text = str_replace('[quote:destination_name]', $this->destinationRepository->countryName, $text);
        }

        if (!empty($this->destinationRepository)) {
            $url = $this->siteRepository->url . '/' . $this->destinationRepository->countryName . '/quote/' .
                $this->quoteRepository->id;
            $text = str_replace(
                '[quote:destination_link]',
                $url,
                $text
            );
        } else {
            $text = str_replace('[quote:destination_link]', '', $text);
        }

        return $text;
    }

    /**
     * @param $text
     * @param $user
     * @return mixed
     */
    private function computeUser($text, $user): string
    {
        if ($user) {
            if (strpos($text, '[user:first_name]') !== false) {
                $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($user->firstname)), $text);
            }
        }
        if (strpos($text, '[user:last_name]') !== false) {
            $text = str_replace('[user:last_name]', ucfirst(mb_strtolower($user->lastname)), $text);
        }
        if (strpos($text, '[user:email]') !== false) {
            $text = str_replace('[user:email]', $user->email, $text);
        }
        return $text;
    }

    /**
     * @param $text
     * @param array $data
     * @return mixed
     */
    private function computeText($text, array $data): string
    {
        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;
        if (array_key_exists('user', $data) && $data['user'] instanceof User) {
            $user = $data['user'];
        } else {
            $user = $this->applicationContext->getCurrentUser();
        }

        //quote
        if ($quote) {
            $this->initQuote($quote);
            $text = $this->computeSummary($text);
        }
        //destination
        $text = $this->computeDestination($text);
        //user
        $text = $this->computeUser($text, $user);

        return $text;
    }
}
