<?php

/**
 * TaskType test.
 */

namespace App\Tests\Form\Type;

use App\Entity\Category;
use App\Entity\Task;
use App\Form\DataTransformer\TagsDataTransformer;
use App\Form\Type\TaskType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TaskTypeTest.
 */
class TaskTypeTest extends KernelTestCase
{
    private FormFactoryInterface $formFactory;
    private TaskType $taskType;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        // IMPORTANT: use STUB, not MOCK (avoids PHPUnit notices)
        $transformer = $this->createStub(TagsDataTransformer::class);
        $transformer->method('transform')->willReturn('');
        $transformer->method('reverseTransform')->willReturn([]);

        $this->formFactory = $container->get(FormFactoryInterface::class);
        $this->taskType = $container->get(TaskType::class);
    }

    /**
     * Test build form.
     */
    public function testBuildForm(): void
    {
        $form = $this->formFactory->create(TaskType::class);

        $this->assertTrue($form->has('title'));
        $this->assertTrue($form->has('category'));
        $this->assertTrue($form->has('tags'));
    }

    /**
     * Test title field configuration.
     */
    public function testTitleFieldConfiguration(): void
    {
        $form = $this->formFactory->create(TaskType::class);
        $config = $form->get('title')->getConfig();

        $this->assertEquals('label.title', $config->getOption('label'));
        $this->assertTrue($config->getOption('required'));
        $this->assertEquals(255, $config->getOption('attr')['max_length']);
    }

    /**
     * Test category field configuration.
     */
    public function testCategoryFieldConfiguration(): void
    {
        $form = $this->formFactory->create(TaskType::class);
        $config = $form->get('category')->getConfig();

        $this->assertEquals(Category::class, $config->getOption('class'));
        $this->assertEquals('label.category', $config->getOption('label'));
        $this->assertEquals('label.none', $config->getOption('placeholder'));
        $this->assertTrue($config->getOption('required'));
    }

    /**
     * Test tags field configuration.
     */
    public function testTagsFieldConfiguration(): void
    {
        $form = $this->formFactory->create(TaskType::class);
        $config = $form->get('tags')->getConfig();

        $this->assertEquals('label.tags', $config->getOption('label'));
        $this->assertFalse($config->getOption('required'));
        $this->assertEquals(128, $config->getOption('attr')['max_length']);
    }

    /**
     * Test choice label callback.
     */
    public function testChoiceLabelCallback(): void
    {
        $form = $this->formFactory->create(TaskType::class);

        $choiceLabel = $form->get('category')->getConfig()->getOption('choice_label');

        $category = new Category();
        $category->setTitle('My Category');

        $this->assertEquals('My Category', $choiceLabel($category));
    }

    /**
     * Test configure options.
     */
    public function testConfigureOptions(): void
    {
        $resolver = new OptionsResolver();

        $this->taskType->configureOptions($resolver);
        $options = $resolver->resolve();

        $this->assertEquals(Task::class, $options['data_class']);
    }

    /**
     * Test get block prefix.
     */
    public function testGetBlockPrefix(): void
    {
        $this->assertEquals('task', $this->taskType->getBlockPrefix());
    }

    /**
     * Test form submission.
     */
    public function testFormSubmission(): void
    {
        $task = new Task();

        $form = $this->formFactory->create(TaskType::class, $task);

        $form->submit([
            'title' => 'Test task',
            'category' => new Category(),
            'tags' => 'php, symfony',
        ]);

        $this->assertTrue($form->isSynchronized());
    }
}
