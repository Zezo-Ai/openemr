<?php

namespace OpenEMR\Tests\Unit\FHIR\SMART\ExternalClinicalDecisionSupport;

use OpenEMR\Common\Auth\OpenIDConnect\Entities\ClientEntity;
use OpenEMR\Common\Twig\TwigContainer;
use OpenEMR\FHIR\Config\ServerConfig;
use OpenEMR\FHIR\SMART\ExternalClinicalDecisionSupport\DecisionSupportInterventionEntity;
use OpenEMR\FHIR\SMART\ExternalClinicalDecisionSupport\PredictiveDSIServiceEntity;
use PHPUnit\Framework\TestCase;

class PredictiveDSIServiceEntityTest extends TestCase
{
    protected DecisionSupportInterventionEntity $entity;
    protected ClientEntity $clientEntity;
    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->clientEntity = new ClientEntity();
        $this->clientEntity->setIdentifier("1");
        $this->clientEntity->setName("Test Client");
        $this->entity = new PredictiveDSIServiceEntity($this->clientEntity);
    }

    public function testPopulateServiceWithFhirQuestionnaire(): void
    {
        $twig = (new TwigContainer())->getTwig();
        $questionnaire = $twig->render("api/smart/dsi-service-questionnaire.json.twig", ['fhirUrl' => '/']);
        $qr = file_get_contents(__DIR__ . "/../../../../data/Unit/FHIR/SMART/ExternalClinicalDecisionSupport/dsi-service-qr-test.json");
//        $this->twig->render("api/smart/dsi-service-qr-test.json.twig");
        $this->entity->populateServiceWithFhirQuestionnaire($questionnaire, $qr);
        $fields = $this->entity->getFields();
        $this->assertCount(31, $fields, "All fields should have been populated");
        $field = $fields[0];
        $this->assertEquals("predictive.details.developer", $field['name']);
        $this->assertEquals('Name and contact information for the intervention developer', $field['label']);
        $this->assertEquals(
            "Dr. Quackers McFeathers, Pond Analytics Inc., email: drquackers@pondanalytics.com",
            $field['value']
        );
    }
}
