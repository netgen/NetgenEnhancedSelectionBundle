<?php
/**
 * File containing the EnhancedSelectionSortExtension class.
 *
 * @copyright Copyright (C) Brookins Consulting. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace Netgen\Bundle\EnhancedSelectionBundle\Twig\Extension;

use Twig_Extension;
use Twig_SimpleFilter;
use InvalidArgumentException;

/**
 * Twig extension providing the 'sckenhancedselection_sort' twig filter
 */
class EnhancedSelectionSortExtension extends Twig_Extension
{
    /**
     * The "sckenhancedselection_sort" twig filter sorts an array of items ( objects or arrays ) by the specified sort by field value
     *
     * Usage: {% set available_options = options|sckenhancedselection_sort( 'priority', 'desc' ) %}
     *
     * @return array The array of Twig_SimpleFilter objects
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter( 'sckenhancedselection_sort', array( $this, 'sortByEnhancedSelectionOptionField' ) )
        );
    }

    /**
     * The "sortByEnhancedSelectionOptionField" method sorts an array of items by the specified sort by field value
     * if the available options priority values do not contain duplicates which returns the options without modifications
     *
     * @param array $content  Array of selection options
     * @param string $sortByField  String of selection sort field. Supports: priority|id|identifier|name. Default: priority
     * @param string $sortDirection  String of selection sort direction. Supports: asc|desc. Default: asc
     *
     * @return array The array of FieldType selection options sorted as best as possible
     */
    public function sortByEnhancedSelectionOptionField( $content, $sortByField = 'priority', $sortDirection = 'asc' )
    {
        $selection_options_field_content_not_sortable = false;

        if( !is_array( $content ) )
        {
            throw new InvalidArgumentException( 'Input passed to sckenhancedselection_sort twig filter is not an array' );
        }
        else if ( $sortByField === null )
        {
            throw new InvalidArgumentException( 'Sort by field parameter passed to the sckenhancedselection_sort twig filter can not be null' );
        }
        else if ( !isset( $content[ 0 ][ $sortByField ] ) )
        {
            throw new InvalidArgumentException( 'Sort by field parameter passed to the sckenhancedselection_sort twig filter must be either: priority, id, identifier or name' );
        }
        else
        {
            for( $i = 0, $j = 0, $n = count( $content ); $i < $n; ++$i )
            {
                for( $g = $i + 1; $g < $n; ++$g )
                {
                    if( $content[ $i ][ $sortByField ] == $content[ $g ][ $sortByField ] )
                    {
                        $selection_options_field_content_not_sortable = true;
                    }
                }

                if( $selection_options_field_content_not_sortable == true )
                {
                    break;
                }
            }

            if( $selection_options_field_content_not_sortable == false )
            {
                usort( $content, self::usortByPriority( $sortByField, $sortDirection ) );
            }
        }

        return $content;
    }

    /**
     * Provides usort value_compare_func function to sort selection options
     *
     * @return int The usort value_compare_func function result to determin sort order
     */
    public function usortByPriority( $sortByField = 'priority', $sortDirection = 'asc' )
    {
        return function ( $a, $b ) use ( $sortByField, $sortDirection )
        {
            $sortDirectionInt = $sortDirection === 'desc' ? -1 : 1;

            if ( $a[ $sortByField ] == $b[ $sortByField ] )
            {
                return 0;
            }
            elseif ( $a[ $sortByField ] > $b[ $sortByField ] )
            {
                return ( 1 * $sortDirectionInt );
            }
            else
            {
                return ( -1 * $sortDirectionInt );
            }
        };
    }

    /**
     * Returns the name of the twig extension.
     *
     * @return string The twig extension name
     */
    public function getName()
    {
        return 'sckenhancedselection_sort';
    }
}
