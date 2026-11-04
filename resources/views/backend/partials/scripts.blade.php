{{-- JS inti template. Plugin khusus halaman (editor, chart, dst.) ditambahkan lewat @push('scripts'). --}}
<script src="{{ asset('be/assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('be/assets/js/popper.min.js') }}"></script>
<script src="{{ asset('be/assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('be/assets/js/modernizr.min.js') }}"></script>
<script src="{{ asset('be/assets/js/detect.js') }}"></script>
<script src="{{ asset('be/assets/js/fastclick.js') }}"></script>
<script src="{{ asset('be/assets/js/jquery.blockUI.js') }}"></script>
<script src="{{ asset('be/assets/js/waves.js') }}"></script>
<script src="{{ asset('be/assets/js/jquery.nicescroll.js') }}"></script>
<script src="{{ asset('be/assets/js/app.js') }}"></script>
<script src="{{ asset('be/assets/plugins/alertify/js/alertify.js') }}"></script>
@stack('scripts')
