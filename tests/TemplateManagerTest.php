<?php

use Template\TemplateManager;
use Template\Entity\Quote;
use Template\Entity\Template;
use Template\Repository\DestinationRepository;
use Template\Repository\SiteRepository;
use Template\Context\ApplicationContext;

class TemplateManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Init the mocks
     */
    public function setUp()
    {
    }

    /**
     * Closes the mocks
     */
    public function tearDown()
    {
    }

    /**
     * @test
     */
    public function test()
    {
        $faker = \Faker\Factory::create();

        $destinationId = $faker->randomNumber();
        $expectedDestination = DestinationRepository::getInstance()->getById($destinationId);

        $expectedUser = ApplicationContext::getInstance()->getCurrentUser();

        $siteId = $faker->randomNumber();
        $expectedSite = SiteRepository::getInstance()->getById($siteId);

        $quote = new Quote($faker->randomNumber(), $siteId, $destinationId, $faker->date());

        $template = new Template(
            1,
            'Votre voyage avec une agence locale [quote:destination_name]',
            "
Bonjour [user:first_name],

Merci d'avoir contacté un agent local pour votre voyage [quote:destination_name].

Bien cordialement,

[quote:destination_link]

L'équipe Evaneos.com
www.evaneos.com
");
        $templateManager = new TemplateManager();

        $message = $templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $quote
            ]
        );

        $this->assertEquals('Votre voyage avec une agence locale ' . $expectedDestination->countryName, $message->subject);
        $this->assertEquals("
Bonjour " . $expectedUser->firstname . ",

Merci d'avoir contacté un agent local pour votre voyage " . $expectedDestination->countryName . ".

Bien cordialement,

".$expectedSite->url.'/'.$expectedDestination->countryName.'/quote/'.$quote->id."

L'équipe Evaneos.com
www.evaneos.com
", $message->content);
    }
}
