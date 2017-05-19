<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\CallbackTransformer;
use AppBundle\Entity\Portfolio;
use AppBundle\Entity\Share;

/**
 * Форма для добавления/редактирования акции в портфеле.
 */
class PortfolioShareType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $portfolioShare = $builder->getData();

        $portfolio = $options['portfolio'];

        $portfolioShares = $portfolio->getPortfolioShares();

        $em = $options['entity_manager'];

        if ($portfolioShare->getId() > 0) {
            $portfolioShares->removeElement($portfolioShare);

            $totalProcents = ($em->getRepository(Portfolio::class)->getTotalProcents($portfolio) - $portfolioShare->getRatio()) * 100;
        } else {
            $totalProcents = $em->getRepository(Portfolio::class)->getTotalProcents($portfolio) * 100;
        }

        $shares = $em->getRepository(Share::class)->findAllWithExclude($portfolioShares);

        $maxRatio = 100 - $totalProcents;

        $builder->add('share', EntityType::class, [
            'class'              => 'AppBundle:Share',
            'choice_label'       => 'name',
            'choices'            => $shares,
            'label'              => 'form.name',
            'translation_domain' => 'labels',
        ])->add('ratio', IntegerType::class, [
            'label'              => 'form.procent',
            'translation_domain' => 'labels',
            'attr'  => [
                'min'   => 0,
                'max'   => $maxRatio,
                'step'  => 1,
            ],
        ])->add('save', SubmitType::class, [
            'label'              => 'form.save',
            'translation_domain' => 'labels',
            'attr' => [
                'class' => 'btn-success'
            ],
        ]);

        $builder->get('ratio')
            ->addModelTransformer(new CallbackTransformer(
                function ($ratioAsNumber) {
                    // Трансформация числа в процент
                    return $ratioAsNumber * 100;
                },
                function ($ratioAsPercent) {
                    // Трансформация процента в число
                    return $ratioAsPercent / 100;
                }
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\PortfolioShare',
            'portfolio' => null,
        ])->setRequired('entity_manager');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_portfolio_share';
    }
}