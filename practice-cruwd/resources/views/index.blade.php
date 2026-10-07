<!DOCTYPE html>
<html>
<head>
    <title>Students</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>

    <h1>Student Management</h1>

    <a href="{{ route('students.create') }}" class="button">
        Add Student
    </a>

    <table>
        <thead>
            <tr>
                <th>Student Number</th>
                <th>Name</th>
                <th>Email</th>
                <th>Program</th>
                <th>Year Level</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

            @forelse ($students as $student)

                <tr>
                    <td>{{ $student->student_number }}</td>

                    <td>
                        {{ $student->first_name }}
                        {{ $student->last_name }}
                    </td>

                    <td>{{ $student->email }}</td>

                    <td>{{ $student->program->name ?? 'No Program' }}</td>

                    <td>{{ $student->year_level }}</td>

                    <td>
                        <a href="{{ route('students.edit', $student) }}">
                            Edit
                        </a>
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="6">
                        No students found.
                    </td>
                </tr>

            @endforelse

        </tbody>
    </table>

</body>
</html>
