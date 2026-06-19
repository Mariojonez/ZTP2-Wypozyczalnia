<?php

/**
 * ChangeStatusType test.
 */

namespace App\Tests\Form\Type;

use App\Entity\Reservation;
use App\Form\Type\ChangeStatusType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ChangeStatusTypeTest.
 */
class ChangeStatusTypeTest extends KernelTestCase
{
    private FormFactoryInterface $formFactory;
    private ChangeStatusType $type;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->formFactory = $container->get(FormFactoryInterface::class);
        $this->type = $container->get(ChangeStatusType::class);
    }

    /**
     * Test build form has status field.
     */
    public function testBuildFormHasStatusField(): void
    {
        $form = $this->formFactory->create(ChangeStatusType::class);

        $this->assertTrue($form->has('status'));
    }

    /**
     * Test status field configuration.
     */
    public function testStatusFieldConfiguration(): void
    {
        $form = $this->formFactory->create(ChangeStatusType::class);

        $config = $form->get('status')->getConfig();

        $this->assertEquals('label.status', $config->getOption('label'));

        $choices = $config->getOption('choices');

        $this->assertArrayHasKey('label.pending', $choices);
        $this->assertArrayHasKey('label.accepted', $choices);
        $this->assertArrayHasKey('label.cancelled', $choices);

        $this->assertEquals('label.pending', $choices['label.pending']);
        $this->assertEquals('label.accepted', $choices['label.accepted']);
        $this->assertEquals('label.cancelled', $choices['label.cancelled']);
    }

    /**
     * Test configure options.
     */
    public function testConfigureOptions(): void
    {
        $resolver = new OptionsResolver();

        $this->type->configureOptions($resolver);
        $options = $resolver->resolve();

        $this->assertArrayHasKey('data_class', $options);
        $this->assertEquals(Reservation::class, $options['data_class']);
    }

    /**
     * Test form submission.
     */
    public function testFormSubmission(): void
    {
        $reservation = new Reservation();

        $form = $this->formFactory->create(ChangeStatusType::class, $reservation);

        $form->submit([
            'status' => 'label.accepted',
        ]);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals('label.accepted', $reservation->getStatus());
    }

    /**
     * Test get block prefix.
     */
    public function testGetBlockPrefix(): void
    {
        $this->assertEquals(
            'change_status',
            (new ChangeStatusType())->getBlockPrefix()
        );
    }
}
