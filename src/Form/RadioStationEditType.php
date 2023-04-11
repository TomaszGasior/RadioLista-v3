<?php

namespace App\Form;

use App\Entity\Embeddable\RadioStation\Appearance;
use App\Entity\Enum\RadioStation\Background;
use App\Entity\Enum\RadioStation\DabChannel;
use App\Entity\Enum\RadioStation\Polarization;
use App\Entity\Enum\RadioStation\Quality;
use App\Entity\Enum\RadioStation\Reception;
use App\Entity\Enum\RadioStation\Type;
use App\Entity\RadioStation;
use App\Form\DataTransformer\RadioStationRdsPsFrameTransformer;
use App\Form\DataTransformer\RadioStationRdsRtFrameTransformer;
use App\Form\Type\DecimalUnitType;
use App\Form\Type\IntegerUnitType;
use App\Form\Type\RadioStationCompletionTextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

class RadioStationEditType extends AbstractType
{
    public function __construct(
        private RadioStationRdsPsFrameTransformer $rdsPsTransformer,
        private RadioStationRdsRtFrameTransformer $rdsRtTransformer,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('frequency', DecimalUnitType::class, [
                'step' => 0.001,
                'scale' => 3,
            ])
            ->add('name', RadioStationCompletionTextType::class)
            ->add('radioGroup', RadioStationCompletionTextType::class, [
                'required' => false,
            ])
            ->add('country', RadioStationCompletionTextType::class, [
                'required' => false,
            ])
            ->add('region', RadioStationCompletionTextType::class, [
                'required' => false,
            ])
            ->add('location', RadioStationCompletionTextType::class, [
                'required' => false,
            ])
            ->add('power', DecimalUnitType::class, [
                'step' => 0.001,
                'scale' => 3,
                'required' => false,
            ])
            ->add('polarization', EnumType::class, [
                'required' => false,
                'class' => Polarization::class,
                'choice_label' => function (Polarization $polarization): string {
                    return 'polarization.'.$polarization->value;
                },
                'placeholder' => new TranslatableMessage('radio_station.edit.form.polarization.choice.'),
            ])
            ->add('multiplex', RadioStationCompletionTextType::class, [
                'required' => false,
            ])
            ->add('dabChannel', EnumType::class, [
                'required' => false,
                'class' => DabChannel::class,
                'choice_label' => function(DabChannel $dabChannel): string {
                    return $dabChannel->value;
                },
                'choice_translation_domain' => false,
                'placeholder' => new TranslatableMessage('radio_station.edit.form.dabChannel.choice.'),
                'attr' => [
                    'data-dab-channel-frequencies' => json_encode(array_reduce(
                        DabChannel::cases(),
                        function(array $data, DabChannel $dabChannel): array {
                            return array_merge($data, [$dabChannel->value => $dabChannel->getFrequency()]);
                        },
                        []
                    )),
                ],
            ])
            ->add('type', EnumType::class, [
                'class' => Type::class,
                'choice_label' => function (Type $type): string {
                    return 'type.'.$type->value;
                },
            ])
            ->add('distance', IntegerUnitType::class, [
                'required' => false,
                'attr' => ['min' => '1'],
            ])
            ->add('maxSignalLevel', IntegerUnitType::class, [
                'required' => false,
            ])
            ->add('reception', EnumType::class, [
                'class' => Reception::class,
                'choice_label' => function (Reception $reception) {
                    return 'reception.'.$reception->value;
                },
            ])
            ->add('quality', EnumType::class, [
                'class' => Quality::class,
                'choice_label' => function (Quality $quality) {
                    return 'quality.'.$quality->value;
                },
            ])
            ->add('firstLogDate', null, [
                'label' => 'column.firstLogDate',
                'attr' => [
                    'placeholder' => new TranslatableMessage('radio_station.edit.form.firstLogDate.help'),
                    'pattern' => '[0-9]{4}(-[0-9]{2}(-[0-9]{2})?)?',
                ],
            ])
            ->add('privateNumber', null, [
                'attr' => ['min' => '1'],
            ])
            ->add('externalAnchor', UrlType::class, [
                'required' => false,
                'label' => 'column.externalAnchor',
                'default_protocol' => null,
                'attr' => [
                    'placeholder' => new TranslatableMessage('radio_station.edit.form.externalAnchor.help'),
                ],
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
            ])
            ->add('rdsPs', TextareaType::class, [
                'property_path' => 'rds.ps',

                'label' => new TranslatableMessage('radio_station.edit.form.rdsPs'),
                'required' => false,
                'trim' => false,
                'attr' => ['placeholder' => new TranslatableMessage('radio_station.edit.form.rdsPs.help')],
            ])
            ->add('rdsRt', TextareaType::class, [
                'property_path' => 'rds.rt',

                'label' => new TranslatableMessage('radio_station.edit.form.rdsRt'),
                'required' => false,
                'trim' => false,
                'attr' => ['placeholder' => new TranslatableMessage('radio_station.edit.form.rdsRt.help')],
            ])
            ->add('rdsPty', null, [
                'property_path' => 'rds.pty',

                'label' => new TranslatableMessage('radio_station.edit.form.rdsPty'),
            ])
            ->add('rdsPi', null, [
                'property_path' => 'rds.pi',

                'label' => new TranslatableMessage('radio_station.edit.form.rdsPi'),
                'attr' => ['maxlength' => '4'],
            ])
            ->add('appearanceBackground', EnumType::class, [
                'property_path' => 'appearance.background',

                'required' => false,
                'label' => new TranslatableMessage('radio_station.edit.form.appearanceBackground'),
                'class' => Background::class,
                'choice_label' => function (Background $background): TranslatableMessage {
                    return new TranslatableMessage('radio_station.edit.form.appearanceBackground.choice.'.$background->value);
                },
                'placeholder' => new TranslatableMessage('radio_station.edit.form.appearanceBackground.choice.'),
            ])
            ->add('appearanceBold', CheckboxType::class, [
                'property_path' => 'appearance.bold',

                'required' => false,
                'label' => new TranslatableMessage('radio_station.edit.form.appearanceBold'),
            ])
            ->add('appearanceItalic', CheckboxType::class, [
                'property_path' => 'appearance.italic',

                'required' => false,
                'label' => new TranslatableMessage('radio_station.edit.form.appearanceItalic'),
            ])
            ->add('appearanceStrikethrough', CheckboxType::class, [
                'property_path' => 'appearance.strikethrough',

                'required' => false,
                'label' => new TranslatableMessage('radio_station.edit.form.appearanceStrikethrough'),
            ])
        ;

        $builder
            ->get('rdsPs')
            ->addViewTransformer($this->rdsPsTransformer)
        ;
        $builder
            ->get('rdsRt')
            ->addViewTransformer($this->rdsRtTransformer)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RadioStation::class,
            'label_format' => 'column.%name%',
            'translation_domain' => 'radio_table',
        ]);
    }
}
