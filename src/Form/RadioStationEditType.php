<?php

namespace App\Form;

use App\Entity\Embeddable\RadioStation\Locality;
use App\Entity\RadioStation;
use App\Form\DataTransformer\RadioStationRdsPsFrameTransformer;
use App\Form\DataTransformer\RadioStationRdsRtFrameTransformer;
use App\Form\Type\DecimalUnitType;
use App\Form\Type\IntegerUnitType;
use App\Form\Type\TextHintsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class RadioStationEditType extends AbstractType
{
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
                'step' => 0.01,
            ])
            ->add('name', TextHintsType::class, [
                'hints' => [
                    'Polskie Radio Jedynka',
                    'Polskie Radio Dwójka',
                    'Polskie Radio Trójka',
                    'Polskie Radio Czwórka',
                    'Polskie Radio 24',
                    'RMF FM',
                    'RMF Classic',
                    'RMF Maxxx',
                    'Zet',
                    'Chillizet',
                    'Meloradio',
                    'AntyRadio',
                    'Eska',
                    'Wawa',
                    'Vox FM',
                    'Plus',
                    'Tok FM',
                    'Złote Przeboje',
                    'Pogoda',
                    'Rock Radio',
                    'Muzo.FM',
                    'Maryja',
                ],
            ])
            ->add('radioGroup', TextHintsType::class, [
                'required' => false,
                'hints' => [
                    'Polskie Radio',
                    'Audytorium 17',
                    'Grupa RMF',
                    'Eurozet',
                    'Grupa Radiowa Time',
                    'Grupa Radiowa Agory',
                ],
            ])
            ->add('country', TextHintsType::class, [
                'required' => false,
                'hints' => [
                    'Polska',
                    'Białoruś',
                    'Czechy',
                    'Litwa',
                    'Niemcy',
                    'Ukraina',
                    'Rosja',
                    'Słowacja',
                ],
            ])
            ->add('location')
            ->add('power', DecimalUnitType::class, [
                'step' => 0.001,
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
            ->add('localityType', ChoiceType::class, [
                'property_path' => 'locality.type',

                'label' => 'column.locality',
                'choices' => [
                    Locality::TYPE_COUNTRY,
                    Locality::TYPE_LOCAL,
                    Locality::TYPE_NETWORK,
                ],
                'choice_label' => function ($choice) {
                    return 'radio_station.edit.form.localityType.choice.'.$choice;
                },
                'choice_translation_domain' => 'messages',
            ])
            ->add('localityCity', TextHintsType::class, [
                'property_path' => 'locality.city',

                'label' => 'radio_station.edit.form.localityCity',
                'translation_domain' => 'messages',
                'required' => false,
                'hints' => [
                    'dolnośląskie',
                    'kujawsko-pomorskie',
                    'lubelskie',
                    'lubuskie',
                    'łódzkie',
                    'małopolskie',
                    'mazowieckie',
                    'opolskie',
                    'podkarpackie',
                    'podlaskie',
                    'pomorskie',
                    'śląskie',
                    'świętokrzyskie',
                    'warmińsko-mazurskie',
                    'wielkopolskie',
                    'zachodniopomorskie',
                ],
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
            ->add('marker', ChoiceType::class, [
                'label' => 'radio_station.edit.form.marker',
                'choices' => [
                    RadioStation::MARKER_NONE,
                    RadioStation::MARKER_1,
                    RadioStation::MARKER_2,
                    RadioStation::MARKER_3,
                    RadioStation::MARKER_4,
                    RadioStation::MARKER_5,
                    RadioStation::MARKER_6,
                ],
                'choice_label' => function ($choice) {
                    return 'radio_station.edit.form.marker.choice.'.$choice;
                },
                'translation_domain' => 'messages',
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
