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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class RadioStationEditType extends AbstractType
{
    public function __construct(
        private RadioStationRdsPsFrameTransformer $rdsPsTransformer,
        private RadioStationRdsRtFrameTransformer $rdsRtTransformer,
        private TranslatorInterface $translator,
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
                'choices' => array_merge([null], Polarization::cases()),
                'choice_label' => function (?Polarization $polarization): string {
                    if ($polarization) {
                        return $this->translator->trans('polarization.'.$polarization->value, [], 'radio_table');
                    }
                    return $this->translator->trans('radio_station.edit.form.polarization.choice.');
                },
                'choice_translation_domain' => false,
            ])
            ->add('multiplex', RadioStationCompletionTextType::class, [
                'required' => false,
            ])
            ->add('dabChannel', EnumType::class, [
                'required' => false,
                'class' => DabChannel::class,
                'choices' => array_merge([null], DabChannel::cases()),
                'choice_label' => function(?DabChannel $dabChannel): ?string {
                    if ($dabChannel) {
                        return $dabChannel->value;
                    }
                    return $this->translator->trans('radio_station.edit.form.dabChannel.choice.');
                },
                'choice_translation_domain' => false,
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
                'label' => $this->translator->trans('column.externalAnchor', [], 'radio_table'),
                'default_protocol' => null,
                'attr' => [
                    'placeholder' => $this->translator->trans('radio_station.edit.form.externalAnchor.help'),
                ],
                'translation_domain' => false,
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
            ->add('appearanceBackground', EnumType::class, [
                'property_path' => 'appearance.background',

                'required' => false,
                'label' => 'radio_station.edit.form.appearanceBackground',
                'class' => Background::class,
                'choice_label' => function (Background $background) {
                    return 'radio_station.edit.form.appearanceBackground.choice.'.$background->value;
                },
                'placeholder' => 'radio_station.edit.form.appearanceBackground.choice.',
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
