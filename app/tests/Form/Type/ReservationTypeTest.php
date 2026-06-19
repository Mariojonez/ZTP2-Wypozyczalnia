<?php

/**
 * ReservationType test.
 */

namespace App\Tests\Form\Type;

use App\Entity\Reservation;
use App\Entity\Task;
use App\Entity\User;
use App\Form\Type\ReservationType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ReservationTypeTest.
 */
class ReservationTypeTest extends KernelTestCase
{
    private FormFactoryInterface $formFactory;
    private ReservationType $type;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->formFactory = $container->get(FormFactoryInterface::class);
        $this->type = $container->get(ReservationType::class);
    }

    /**
     * Test build form has all fields.
     */
    public function testBuildFormHasAllFields(): void
    {
        $form = $this->formFactory->create(ReservationType::class);

        $this->assertTrue($form->has('user'));
        $this->assertTrue($form->has('task'));
        $this->assertTrue($form->has('status'));
        $this->assertTrue($form->has('comment'));
    }

    /**
     * Test user field configuration.
     */
    public function testUserFieldConfiguration(): void
    {
        $form = $this->formFactory->create(ReservationType::class);

        $config = $form->get('user')->getConfig();

        $this->assertEquals(User::class, $config->getOption('class'));
        $this->assertEquals('label.user', $config->getOption('label'));
        $this->assertTrue($config->getOption('disabled'));
    }

    /**
     * Test task field configuration.
     */
    public function testTaskFieldConfiguration(): void
    {
        $form = $this->formFactory->create(ReservationType::class);

        $config = $form->get('task')->getConfig();

        $this->assertEquals(Task::class, $config->getOption('class'));
        $this->assertEquals('label.select_task', $config->getOption('label'));
        $this->assertEquals('placeholder.select_task', $config->getOption('placeholder'));
        $this->assertTrue($config->getOption('required'));
    }

    /**
     * Test status field configuration.
     */
    public function testStatusFieldConfiguration(): void
    {
        $form = $this->formFactory->create(ReservationType::class);

        $config = $form->get('status')->getConfig();

        $this->assertEquals('label.status', $config->getOption('label'));
        $this->assertTrue($config->getOption('disabled'));

        $choices = $config->getOption('choices');

        $this->assertArrayHasKey('label.pending', $choices);
        $this->assertEquals('label', $choices['label.pending']);
    }

    /**
     * Test user choice label configuration.
     */
    public function testUserChoiceLabelConfiguration(): void
    {
        $form = $this->formFactory->create(ReservationType::class);

        $config = $form->get('user')->getConfig();

        $this->assertSame('email', $config->getOption('choice_label'));
    }

    /**
     * Test task choice label configuration.
     */
    public function testTaskChoiceLabelConfiguration(): void
    {
        $form = $this->formFactory->create(ReservationType::class);

        $config = $form->get('task')->getConfig();

        $this->assertSame('title', $config->getOption('choice_label'));
    }

    /**
     * Test configure options.
     */
    public function testConfigureOptions(): void
    {
        $resolver = new OptionsResolver();

        $this->type->configureOptions($resolver);

        $options = $resolver->resolve();

        $this->assertEquals(Reservation::class, $options['data_class']);
    }

    /**
     * Test get block prefix.
     */
    public function testGetBlockPrefix(): void
    {
        $this->assertEquals(
            'reservation',
            (new ReservationType())->getBlockPrefix()
        );
    }
}
