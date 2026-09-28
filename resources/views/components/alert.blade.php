<div {{ $attributes->merge(['class' => 'alert ' . $kelas(), 'role' => 'alert']) }}>
    {{ $slot }}
</div>
