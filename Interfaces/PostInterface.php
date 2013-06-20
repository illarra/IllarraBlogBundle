<?php

namespace Illarra\BlogBundle\Interfaces;

interface PostInterface
{
    public function getId();
    public function setPublishedAt($publishedAt);
    public function getPublishedAt();
    public function addLabel(LabelInterface $label);
    public function removeLabel(LabelInterface $label);
    public function getLabels();
    public function getUrlRoute();
    public function getUrlParams($locale = false);
}