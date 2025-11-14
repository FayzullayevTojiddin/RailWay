@php
    // If $parent is already a View object/instance, render it directly
    // Otherwise, treat it as a string (already rendered content)
@endphp

<div class="fi-section">
    {!! is_string($parent) ? $parent : $parent->render() !!}
</div>
