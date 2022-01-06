<?php

namespace App\Form;

use App\Entity\Embeddable\RadioStation\Appearance;
use App\Entity\RadioStation;
use App\Form\DataTransformer\RadioStationRdsPsFrameTransformer;
use App\Form\DataTransformer\RadioStationRdsRtFrameTransformer;
use App\Form\Type\DecimalUnitType;
use App\Form\Type\IntegerUnitType;
use App\Form\Type\RadioStationCompletionTextType;
use App\Util\Data\DabChannelsTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class RadioStationEditType extends AbstractType
{
    use DabChannelsTrait;

    private $rdsPsTransformer;
    private $rdsRtTransformer;
    private $translator;

    public function __construct(RadioStationRdsPsFrameTransformer $rdsPsTransformer,
                                RadioStationRdsRtFrameTransformer $rdsRtTransformer,
                                TranslatorInterface $translator)
    {
        $this->rdsPsTransformer = $rdsPsTransformer;
        $this->rdsRtTransformer = $rdsRtTransformer;
        $this->translator = $translator;
    }

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
            ->add('polarization', ChoiceType::class, [
                'choices' => [
                    RadioStation::POLARIZATION_NONE,
                    RadioStation::POLARIZATION_HORIZONTAL,
                    RadioStation::POLARIZATION_VERTICAL,
                    RadioStation::POLARIZATION_CIRCULAR,
                    RadioStation::POLARIZATION_VARIOUS,
                ],
                'choice_label' => function ($choice) {
                    if ($choice) {
                        return $this->translator->trans('polarization.'.$choice, [], 'radio_table');
                    }
                    return $this->translator->trans('radio_station.edit.form.polarization.choice.'.$choice);
                },
                'choice_translation_domain' => false,
            ])
            ->add('multiplex', RadioStationCompletionTextType::class, [
                'required' => false,
            ])
            ->add('dabChannel', ChoiceType::class, [
                'required' => false,
                'choices' => array_merge([null], $this->getDabChannels()),
                'choice_label' => function ($choice) {
                    if ($choice) {
                        return $choice;
                    }
                    return $this->translator->trans('radio_station.edit.form.dabChannel.choice.'.$choice);
                },
                'choice_translation_domain' => false,

                // These settings are needed for frontend JS script of radio station edit/add page.
                'choice_value' => function ($choice) {
                    return $choice;
                },
                'attr' => [
                    'data-dab-channel-frequencies' => json_encode($this->getDabChannelsWithFrequencies()),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    RadioStation::TYPE_MUSIC,
                    RadioStation::TYPE_UNIVERSAL,
                    RadioStation::TYPE_INFORMATION,
                    RadioStation::TYPE_RELIGIOUS,
                    RadioStation::TYPE_OTHER,
                ],
                'choice_label' => function ($choice) {
                    return 'type.'.$choice;
                },
            ])
            ->add('distance', IntegerUnitType::class, [
                'required' => false,
                'attr' => ['min' => '1'],
            ])
            ->add('maxSignalLevel', IntegerUnitType::class, [
                'required' => false,
            ])
            ->add('reception', ChoiceType::class, [
                'choices' => [
                    RadioStation::RECEPTION_REGULAR,
                    RadioStation::RECEPTION_TROPO,
                    RadioStation::RECEPTION_SCATTER,
                    RadioStation::RECEPTION_SPORADIC_E,
                ],
                'choice_label' => function ($choice) {
                    return 'reception.'.$choice;
                },
            ])
            ->add('quality', ChoiceType::class, [
                'choices' => [
                    RadioStation::QUALITY_VERY_GOOD,
                    RadioStation::QUALITY_GOOD,
                    RadioStation::QUALITY_MIDDLE,
                    RadioStation::QUALITY_BAD,
                    RadioStation::QUALITY_VERY_BAD,
                ],
                'choice_label' => function ($choice) {
                    return 'quality.'.$choice;
                },
            ])
            ->add('firstLogDate', null, [
                'label' => $this->translator->trans('column.firstLogDate', [], 'radio_table'),
                'attr' => [
                    'placeholder' => $this->translator->trans('radio_station.edit.form.firstLogDate.help'),
                    'pattern' => '[0-9]{4}(-[0-9]{2}(-[0-9]{2})?)?',
                ],
                'translation_domain' => false,
            ])
            ->add('privateNumber', null, [
                'attr' => ['min' => '1'],
            ])
            ->add('externalAnchor', UrlType::class, [
                'required' => false,
                'default_protocol' => null,
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
            ])
            ->add('rdsPs', TextareaType::class, [
                'property_path' => 'rds.ps',

                'label' => 'radio_station.edit.form.rdsPs',
                'translation_domain' => 'messages',
                'required' => false,
                'trim' => false,
                'attr' => ['placeholder' => 'radio_station.edit.form.rdsPs.help'],
            ])
            ->add('rdsRt', TextareaType::class, [
                'property_path' => 'rds.rt',

                'label' => 'radio_station.edit.form.rdsRt',
                'translation_domain' => 'messages',
                'required' => false,
                'trim' => false,
                'attr' => ['placeholder' => 'radio_station.edit.form.rdsRt.help'],
            ])
            ->add('rdsPty', null, [
                'property_path' => 'rds.pty',

                'label' => 'radio_station.edit.form.rdsPty',
                'translation_domain' => 'messages',
            ])
            ->add('rdsPi', null, [
                'property_path' => 'rds.pi',

                'label' => 'radio_station.edit.form.rdsPi',
                'translation_domain' => 'messages',
                'attr' => ['maxlength' => '4'],
            ])
            ->add('appearanceBackground', ChoiceType::class, [
                'property_path' => 'appearance.background',

                'label' => 'radio_station.edit.form.appearanceBackground',
                'choices' => [
                    Appearance::BACKGROUND_NONE,
                    Appearance::BACKGROUND_RED,
                    Appearance::BACKGROUND_GREEN,
                    Appearance::BACKGROUND_BLUE,
                    Appearance::BACKGROUND_YELLOW,
                    Appearance::BACKGROUND_PINK,
                ],
                'choice_label' => function ($choice) {
                    return 'radio_station.edit.form.appearanceBackground.choice.'.$choice;
                },
                'translation_domain' => 'messages',
            ])
            ->add('appearanceBold', CheckboxType::class, [
                'property_path' => 'appearance.bold',

                'required' => false,
                'label' => 'radio_station.edit.form.appearanceBold',
                'translation_domain' => 'messages',
            ])
            ->add('appearanceItalic', CheckboxType::class, [
                'property_path' => 'appearance.italic',

                'required' => false,
                'label' => 'radio_station.edit.form.appearanceItalic',
                'translation_domain' => 'messages',
            ])
            ->add('appearanceStrikethrough', CheckboxType::class, [
                'property_path' => 'appearance.strikethrough',

                'required' => false,
                'label' => 'radio_station.edit.form.appearanceStrikethrough',
                'translation_domain' => 'messages',
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
