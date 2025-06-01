<!DOCTYPE html>
<html>
<head>
    <title>Employees List</title>
    <style>
         body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .header {
            /* background-color: #FFA500; Orange */
            color: #fff;
            padding: 10px;
            text-align: center;
            display: flex;
            align-items: center;
        }

        .header img {
            height: 40px;
            margin-right: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .title {
            text-align: center;
            margin: 20px 0 10px 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            word-break: break-word;
            white-space: normal;
        }

        /* Adjusted column widths */
        th:nth-child(1), td:nth-child(1) { width: 12%; } /* Name */
        th:nth-child(2), td:nth-child(2) { width: 12%; } /* Position */
        th:nth-child(3), td:nth-child(3) { width: 10%; }  /* Department */
        th:nth-child(4), td:nth-child(4) { width: 10%; } /* Hire Date */
        th:nth-child(5), td:nth-child(5) { width: 18%; } /* Salary */
    </style>
</head>
<body>

    {{-- <div class="header">
        <img src="{{ public_path('images/1746857717_Admin-Profile.png') }}">
        <h1>Employee System</h1>
    </div> --}}

    <div class="title"><strong>Employees List</strong></div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Department</th>
                <th>Hire Date</th>
                <th>Salary</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
            <tr>
                <td>{{ $employee->user->name ?? 'No user' }}</td>
                <td>{{ $employee->position }}</td>
                <td>{{ $employee->department }}</td>
                <td>{{ $employee->hire_date }}</td>
                <td>{{ $employee->salary }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
