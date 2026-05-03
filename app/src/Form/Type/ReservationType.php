<?php
/**
 * Reservation type.
 */

namespace App\Form\Type;

use App\Entity\Reservation;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ReservationType.
 */
class ReservationType extends AbstractType
{
    /**
     * @var string|null
     */
    public $userEmail;

    /**
     * Constructor.
     *
     * @param string|null $userEmail Optional email address for initialization
     */
    public function __construct(?string $userEmail = null)
    {
        $this->userEmail = $userEmail;
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options Form options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'user',
                EntityType::class,
                [
                    'class' => User::class,
                    'choice_label' => 'email',
                    'label' => 'label.user',
                    'disabled' => true,
                ]
            )
            ->add(
                'task',
                EntityType::class,
                [
                    'class' => Task::class,
                    'choice_label' => 'title',
                    'label' => 'label.select_task',
                    'placeholder' => 'placeholder.select_task',
                    'required' => true,
                ]
            )
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'label.status',
                    'choices' => [
                        'label.pending' => 'label',
                    ],
                    'disabled' => true,
                    'attr' => ['class' => 'form-control'],
                ]
            )
            ->add(
                'comment',
                TextareaType::class,
                [
                    'label' => 'label.comment',
                    'required' => false,
                ]
            );
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
