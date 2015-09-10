<?php

namespace Hellofresh\DoctrineTutorial\Common;

use Doctrine\Common\Collections\Collection;

/**
 * We're modelling our Collection after Doctrine's ArrayCollection.
 * It provides very sane defaults but there's no reason why we should
 * have too strong a dependency on it.
 *
 * If we decide to change to some other Collection library, we can. Just
 * Write an adapter for it.
 *
 * In an actual production project this interface would be smaller. We're just
 * hinting at a good practice here, not taking the time to fully follow through.
 */
interface CollectionInterface extends Collection
{

}
