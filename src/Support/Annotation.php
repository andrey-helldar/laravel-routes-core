<?php

namespace Helldar\LaravelRoutesCore\Support;

use Doctrine\Common\Annotations\AnnotationReader;
use Illuminate\Support\Str;
use ReflectionClass;

final class Annotation
{
    public function summary(string $controller, string $method = null): ?string
    {
        if ($comment = $this->docComment($controller, $method)) {
            return $comment;
        }

        return null;
    }

    public function description(string $controller, string $method = null): ?string
    {
        if ($comment = $this->docComment($controller, $method)) {
            return $comment;
        }

        return null;
    }

    /**
     * Determines if a class or method is deprecated.
     *
     * @param  string  $controller
     * @param  string|null  $method
     *
     * @throws \ReflectionException
     *
     * @return bool
     */
    public function isDeprecated(string $controller, string $method = null)
    {
        if (is_null($method)) {
            [$controller, $method] = $this->parse($controller);
        }

        return $this->isDeprecatedClass($controller) || $this->isDeprecatedMethod($controller, $method);
    }

    /**
     * Determines if a method is deprecated.
     *
     * @param  string  $controller
     * @param  string  $method
     *
     * @throws \ReflectionException
     *
     * @return bool
     */
    public function isDeprecatedMethod(string $controller, string $method)
    {
        if ($item = $this->getReflectionMethod($controller, $method)) {
            return $this->contains($item->getDocComment());
        }

        return false;
    }

    /**
     * Determines if a class is deprecated.
     *
     * @param  string  $controller
     *
     * @throws \ReflectionException
     *
     * @return bool
     */
    public function isDeprecatedClass(string $controller)
    {
        if ($item = $this->reflectionClass($controller)) {
            return $this->contains($item->getDocComment());
        }

        return false;
    }

    /**
     * Parsing a string into a class and method.
     *
     * @param  string  $action
     *
     * @return array
     */
    protected function parse(string $action)
    {
        return [
            Str::before($action, '@'),
            Str::after($action, '@'),
        ];
    }

    /**
     * Determines if an deprecated method label exists in a string.
     *
     * @param $haystack
     *
     * @return bool
     */
    protected function contains($haystack)
    {
        return Str::contains($haystack, '@deprecated');
    }

    /**
     * Annotation Reader Instance.
     *
     * @return \Doctrine\Common\Annotations\AnnotationReader
     */
    protected function reader()
    {
        return new AnnotationReader();
    }

    /**
     * Getting class reflection instance.
     *
     * @param  string  $class
     *
     * @throws \ReflectionException
     *
     * @return \ReflectionClass
     */
    protected function reflectionClass(string $class)
    {
        return new ReflectionClass($class);
    }

    /**
     * Getting method reflection instance from reflection class.
     *
     * @param  \ReflectionClass  $class
     * @param  string  $method
     *
     * @throws \ReflectionException
     *
     * @return \ReflectionMethod|null
     */
    protected function reflectionMethod(ReflectionClass $class, string $method)
    {
        return $class->hasMethod($method)
            ? $class->getMethod($method)
            : null;
    }

    /**
     * Getting class reflection instance.
     *
     * @param  string  $controller
     * @param  string  $method
     *
     * @throws \ReflectionException
     *
     * @return \ReflectionMethod|null
     */
    protected function getReflectionMethod(string $controller, string $method)
    {
        return $this->reflectionMethod(
            $this->reflectionClass($controller),
            $method
        );
    }

    protected function docComment(string $controller, string $method = null): ?string
    {
        $item = is_null($method)
            ? $this->reflectionClass($controller)
            : $this->reflectionMethod($controller, $method);

        return $item->getDocComment() ?: null;
    }
}
