<?php
//

/**
 * Description of PagesForm
 *
 * @author giansimon
 */
namespace AlphaLemon\Block\SearchBlockBundle\Core\Form\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('searchText', 'text');
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'AlphaLemon\Block\SearchBlockBundle\Core\Form\Search\Search',
        );
    }
    
    public function getName()
    {
        return 'search_box';
    }
}