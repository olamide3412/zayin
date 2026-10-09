<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Solar Load Sizing Report - Zayin Technologies</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-title {
            font-size: 20px;
            font-weight: bold;
            color: #020617;
            text-transform: uppercase;
        }
        .company-tagline {
            font-size: 10px;
            color: #f59e0b;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .company-info {
            font-size: 9px;
            color: #64748b;
            margin-top: 5px;
        }
        .badge {
            display: inline-block;
            background-color: #fef3c7;
            color: #92400e;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #020617;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-top: 18px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 9px;
            text-transform: uppercase;
            text-align: left;
            padding: 6px 8px;
            border-bottom: 1px solid #cbd5e1;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .cards-grid {
            width: 100%;
            margin-bottom: 15px;
        }
        .card-cell {
            width: 50%;
            padding: 6px;
            vertical-align: top;
        }
        .card-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
        }
        .card-title {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
        }
        .card-val {
            font-size: 18px;
            font-weight: bold;
            color: #020617;
            margin: 3px 0;
        }
        .card-desc {
            font-size: 9px;
            color: #475569;
        }
        .disclaimer-box {
            background-color: #fefce8;
            border: 1px solid #fef08a;
            padding: 10px;
            border-radius: 6px;
            font-size: 9px;
            color: #713f12;
            line-height: 1.4;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="margin: 0;">
            <tr>
                <td style="border: none; padding: 0;">
                    <div class="company-title">ZAYIN<span style="color: #f59e0b;">TECH</span></div>
                    <div class="company-tagline">Engineered Solar, Starlink &amp; DSTV Solutions</div>
                    <div class="company-info">
                        Headquarters: Abraka, Ethiope East, Delta State, Nigeria<br/>
                        Phone: {{ $companyPhone ?? '+234 803 123 4567' }} | Email: support@zayin.ng | Nationwide Field Service
                    </div>
                </td>
                <td style="border: none; padding: 0; text-align: right; vertical-align: top;">
                    <span class="badge">Official Solar Sizing Estimate</span>
                    <div style="font-size: 9px; color: #64748b; margin-top: 5px;">
                        Ref: <strong>{{ substr($calculation->uuid, 0, 13) }}</strong><br/>
                        Date: {{ $calculation->created_at->format('d M Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">1. Executive System Recommendation</div>
    <table class="cards-grid">
        <tr>
            <td class="card-cell">
                <div class="card-box">
                    <div class="card-title">Inverter System</div>
                    <div class="card-val">{{ $results['inverter']['kva'] }} kVA Pure Sine Wave</div>
                    <div class="card-desc">
                        DC Architecture: <strong>{{ $results['inverter']['voltage'] }} Volts</strong><br/>
                        Continuous load headroom: 25% safety factor included.
                    </div>
                </div>
            </td>
            <td class="card-cell">
                <div class="card-box">
                    <div class="card-title">Battery Storage Bank</div>
                    <div class="card-val">{{ number_format($results['battery']['bankWh'] / 1000, 1) }} kWh (≈ {{ $results['battery']['bankAh'] }} Ah)</div>
                    <div class="card-desc">
                        Battery Type: <strong>{{ ucfirst($results['battery']['type']) }}</strong><br/>
                        Configuration: {{ $results['battery']['unitsNeeded'] }}x {{ $results['battery']['unitLabel'] }}
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="card-cell">
                <div class="card-box">
                    <div class="card-title">Solar Array Generation</div>
                    <div class="card-val">{{ number_format($results['solar']['arrayW']) }} Watts Peak</div>
                    <div class="card-desc">
                        Modules: <strong>{{ $results['solar']['panels'] }}x {{ $results['solar']['panelW'] }}W</strong> Tier-1 Monocrystalline<br/>
                        Est. Daily Yield: ≈ {{ number_format($results['solar']['estDailyGenWh'] / 1000, 1) }} kWh/day
                    </div>
                </div>
            </td>
            <td class="card-cell">
                <div class="card-box">
                    <div class="card-title">MPPT Charge Controller</div>
                    <div class="card-val">{{ $results['controller']['recommendedA'] }} Amps MPPT</div>
                    <div class="card-desc">
                        Calculated charge current: {{ $results['controller']['mpptAmps'] }} A<br/>
                        High-efficiency maximum power point tracker.
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">2. Electrical Load Profile Schedule</div>
    <table>
        <thead>
            <tr>
                <th>Appliance Description</th>
                <th class="text-center">Rated Watts</th>
                <th class="text-center">Quantity</th>
                <th class="text-center">Hours / Day</th>
                <th class="text-right">Daily Energy (Wh)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inputs['appliances'] as $app)
            <tr>
                <td class="font-bold">{{ $app['name'] }}</td>
                <td class="text-center">{{ $app['watts'] }} W</td>
                <td class="text-center">{{ $app['qty'] }}</td>
                <td class="text-center">{{ $app['hours'] }} hrs</td>
                <td class="text-right font-bold">{{ number_format($app['watts'] * $app['qty'] * $app['hours']) }} Wh</td>
            </tr>
            @endforeach
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="4">TOTAL DAILY CONSUMPTION</td>
                <td class="text-right font-bold" style="color: #020617;">
                    {{ number_format($results['energy']['dailyWh']) }} Wh (≈ {{ number_format($results['energy']['dailyWh'] / 1000, 2) }} kWh)
                </td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">3. Operational Summary</div>
    <table>
        <tr>
            <td style="width: 25%; font-weight: bold; background: #f8fafc;">Continuous Peak Load</td>
            <td style="width: 25%;">{{ $results['energy']['peakW'] }} Watts</td>
            <td style="width: 25%; font-weight: bold; background: #f8fafc;">Inductive Motor Surge</td>
            <td style="width: 25%;">{{ $results['energy']['surgePeakW'] }} Watts Peak</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background: #f8fafc;">Target Backup Duration</td>
            <td>{{ $inputs['backup_hours'] }} Hours</td>
            <td style="font-weight: bold; background: #f8fafc;">System Mode</td>
            <td>{{ $inputs['goal'] === 'full_solar' ? 'Full Off-Grid / Solar First' : 'Outage Backup Power' }}</td>
        </tr>
    </table>

    <div class="disclaimer-box">
        <strong>Official Engineering Disclaimer:</strong>
        This sizing calculation is an engineering estimate based on the electrical appliances and usage patterns entered. Actual system performance varies with appliance power factors, ambient site temperatures, seasonal sunlight, roof orientation/shading, wire distances, and hardware brands. A certified Zayin Technologies technician will survey your site before final deployment.
    </div>

    <div class="footer">
        Zayin Technologies Limited &bull; Abraka, Ethiope East, Delta State &bull; Engineering Field Deployments Across All 36 States + FCT &bull; www.zayin.ng
    </div>
</body>
</html>
