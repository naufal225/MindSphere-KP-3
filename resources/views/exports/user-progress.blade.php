<table>
    <thead>
        <tr>
            <th style="background-color: #3498DB; color: white; font-weight: bold;">Nama</th>
            <th style="background-color: #3498DB; color: white; font-weight: bold;">Kelas</th>
            <th style="background-color: #3498DB; color: white; font-weight: bold;">XP Total</th>
            <th style="background-color: #3498DB; color: white; font-weight: bold;">Habit Done</th>
            <th style="background-color: #3498DB; color: white; font-weight: bold;">Challenge Done</th>
            <th style="background-color: #3498DB; color: white; font-weight: bold;">Reflections</th>
            <th style="background-color: #3498DB; color: white; font-weight: bold;">Avg Mood</th>
            <th style="background-color: #3498DB; color: white; font-weight: bold;">Active Days</th>
            <th style="background-color: #3498DB; color: white; font-weight: bold;">Level</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $item['user']->name }}</td>
            <td>{{ $item['class_name'] }}</td>
            <td>{{ number_format($item['xp_total']) }}</td>
            <td>{{ $item['habits_completed'] }}</td>
            <td>{{ $item['challenges_completed'] }}</td>
            <td>{{ $item['reflections_written'] }}</td>
            <td>{{ $item['average_mood'] }}</td>
            <td>{{ $item['activity_days'] }}</td>
            <td>{{ $item['level'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
