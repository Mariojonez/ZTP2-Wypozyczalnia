<?php

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

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->formFactory = $container->get(FormFactoryInterface::class);
        $this->type = $container->get(ChangeStatusType::class);
    }

    public function testBuildFormHasStatusField(): void
    {
        // when
        $form = $this->formFactory->create(ChangeStatusType::class);

        // then
        $this->assertTrue($form->has('status'));
    }

    public function testStatusFieldConfiguration(): void
    {
        // given
        $form = $this->formFactory->create(ChangeStatusType::class);

        // when
        $config = $form->get('status')->getConfig();

        // then
        $this->assertEquals('label.status', $config->getOption('label'));

        $choices = $config->getOption('choices');

        $this->assertArrayHasKey('label.pending', $choices);
        $this->assertArrayHasKey('label.accepted', $choices);
        $this->assertArrayHasKey('label.cancelled', $choices);

        $this->assertEquals('label.pending', $choices['label.pending']);
        $this->assertEquals('label.accepted', $choices['label.accepted']);
        $this->assertEquals('label.cancelled', $choices['label.cancelled']);
    }

    public function testConfigureOptions(): void
    {
        // given
        $resolver = new OptionsResolver();

        // when
        $this->type->configureOptions($resolver);
        $options = $resolver->resolve();

        // then
        $this->assertArrayHasKey('data_class', $options);
        $this->assertEquals(Reservation::class, $options['data_class']);
    }

    public function testFormSubmission(): void
    {
        // given
        $reservation = new Reservation();

        $form = $this->formFactory->create(ChangeStatusType::class, $reservation);

        // when
        $form->submit([
            'status' => 'label.accepted',
        ]);

        // then
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals('label.accepted', $reservation->getStatus());
    }

    public function testGetBlockPrefix(): void
    {
        $this->assertEquals(
            'change_status',
            (new ChangeStatusType())->getBlockPrefix()
        );
    }
}
