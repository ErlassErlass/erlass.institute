# Design Spec: OpenTelemetry & Sentry Tracing Integration

**Date:** 2026-05-18
**Topic:** Application Observability and Performance Tracing
**Status:** Draft (Ready for future implementation)

## 1. Overview
This specification outlines the integration of **OpenTelemetry (OTel)** standards using the existing **Sentry** infrastructure. The goal is to provide deep visibility into the application's request lifecycle, database query performance, and background job execution.

## 2. Objectives
- **Identify Bottlenecks:** Pinpoint slow SQL queries or heavy view rendering in real-time.
- **Trace Lifecycles:** Track a single request from the browser through the controller to the final database response.
- **Performance Baselines:** Establish "normal" load times for critical pages like `Rekap Absensi` and `Dashboard`.

## 3. Architecture & Tools
- **Standard:** OpenTelemetry (OTel)
- **SDK:** `sentry/sentry-laravel` (v4.x or higher)
- **Data Backend:** Sentry Performance Monitoring
- **Sampling:** 10% (0.1) production sampling rate to ensure zero impact on server CPU.

## 4. Implementation Strategy

### 4.1 Configuration (`.env`)
To enable tracing, the following environment variables must be set:
```env
SENTRY_TRACES_SAMPLE_RATE=0.1
SENTRY_SEND_DEFAULT_PII=true
```

### 4.2 Middleware & Auto-Instrumentation
The `Sentry\Laravel\Tracing\Middleware` is already present in `bootstrap/app.php`. Once the sample rate is increased from `0`, it will automatically begin capturing:
- **HTTP Request Duration**
- **Database Query Spans** (SQL statements and execution time)
- **Blade Template Rendering Time**

### 4.3 Manual Instrumentation (Custom Spans)
For complex business logic (e.g., the attendance chunking logic), developers should use manual spans:
```php
\Sentry\trace(function () use ($data) {
    // Perform complex rekap-absensi calculation here
}, 'absensi.rekap_calculation');
```

## 5. Security & Privacy
- **Data Scrubbing:** Ensure sensitive data (passwords, tokens) are filtered out of breadcrumbs and traces (handled by Sentry default filters).
- **Default PII:** Restricted to user IDs for easier debugging of specific user reports.

## 6. Maintenance
- **Review Traces:** Monthly review of the "Slowest Queries" report in the Sentry dashboard.
- **Threshold Alerts:** Set up Sentry alerts if the P95 response time for the dashboard exceeds 2 seconds.
