<form action="{{ route('students.store') }}" method="POST">

    @csrf

    <!-- your inputs -->

    <button type="submit">
        Save Student
    </button>

</form>