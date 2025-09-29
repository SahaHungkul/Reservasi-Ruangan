<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reservasi Ruangan</title>
</head>

<body>
    <h2>Halo,</h2>

    @if ($type === 'pending')
        <p>Ada reservasi baru dari <strong>{{ $reservation->user->name }}</strong>.</p>
        <p>Ruangan: {{ $reservation->room->name }}</p>
        <p>Tanggal: {{ $reservation->date }}</p>
        <p>Jam: {{ $reservation->start_time }} - {{ $reservation->end_time }}</p>
        <p>Status: <strong>Pending</strong></p>
    @elseif ($type === 'approved')
        <p>Reservasi Anda telah <strong>Disetujui</strong>.</p>
        <p>Ruangan: {{ $reservation->room->name }}</p>
        <p>Tanggal: {{ $reservation->date }}</p>
        <p>Jam: {{ $reservation->start_time }} - {{ $reservation->end_time }}</p>
    @elseif ($type === 'rejected')
        <p>Reservasi Anda <strong>Ditolak</strong>.</p>
        <p>Alasan: {{ $reservation->reason }}</p>
        <p>Ruangan: {{ $reservation->room->name }}</p>
        <p>Tanggal: {{ $reservation->date }}</p>
        <p>Jam: {{ $reservation->start_time }} - {{ $reservation->end_time }}</p>
    @elseif ($type === 'canceled')
        <p>Karyawan telah cancel reservasi</p>
        <p>Alasan:{{ $reservation->reason }} </p>
        <p>Ruangan:{{ $reservation->room->name }} </p>
        <p>Tanggal: {{ $reservation->date }}</p>
        <p>Jam: {{ $reservation->start_time }} - {{ $reservation->end_time }}</p>
    @endif

    <br>
    <p>Terima kasih.</p>
</body>

</html>
