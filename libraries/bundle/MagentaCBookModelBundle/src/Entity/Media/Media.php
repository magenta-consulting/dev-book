<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Magenta\Bundle\CBookModelBundle\Entity\Media;

//use AppBundle\Event\UndefinedMethodEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Magenta\Bundle\CBookModelBundle\Entity\Media\Base\AppMedia;


/**
 * This file has been generated by the Sonata EasyExtends bundle.
 *
 * @link https://sonata-project.org/bundles/easy-extends
 *
 * References :
 *   working with object : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 *
 * @author <yourname> <youremail>
 */
/**
 * @ORM\Entity
 * @ORM\Table(name="media__media")
 *
 *
 */
class Media extends AppMedia
{

    function __construct()
    {
    	parent::__construct();
    }
    public function getPublicMediaUrl(){
    return 'https://product.hstatic.net/1000149700/product/cosplay-nu-thuy-thu-sexy-8_1024x1024.jpg';
    }
    public function getMediaExtension()
    {
        $ext = $this->getExtension();
        if (empty($ext)) {
            $metadata = $this->getProviderMetadata();
            if (isset($metadata['provider_name'])) {
                $ext = strtolower($metadata['provider_name']);
            }
            /**
             *
             * if (isset($metadata['filename'])) {
             * $filename = $metadata['filename'];
             * $ext = pathinfo($filename, PATHINFO_EXTENSION);
             * }
             */
        }
        return $ext;
    }

    public function setEnabled($enabled)
    {
        parent::setEnabled($enabled);
        if ($enabled === false) {
            if (($galleryItems = $this->galleryHasMedias) instanceof Collection) {
                foreach ($galleryItems as $item) {
                    $item->setEnabled($enabled);
                }
            }
        }
    }


}
