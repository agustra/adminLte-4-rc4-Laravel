/**
 * Format date utilities
 * Available formats: 7
 * - formatDate(): YYYY-MM-DD format
 * - getTodayString(): Today as YYYY-MM-DD
 * - formatDateIndonesian(): Indonesian locale format
 * - formatDateShort(): Kam 11 Sep 2025
 * - formatDateLong(): Kamis 11 September 2025
 * - formatDateTime(): Kamis 11 Sep 2025 06.47
 * - formatDateTimeFull(): Kamis 11 September 2025 06.47.30
 */

/**
 * Format date to YYYY-MM-DD string
 * @param {Date} date - Date object to format
 * @returns {string} Formatted date string (YYYY-MM-DD)
 */
export const formatDate = (date) => {
    return [
        date.getFullYear(),
        String(date.getMonth() + 1).padStart(2, "0"),
        String(date.getDate()).padStart(2, "0"),
    ].join("-");
};

/**
 * Get today's date as YYYY-MM-DD string
 * @returns {string} Today's date in YYYY-MM-DD format
 */
export const getTodayString = () => {
    return formatDate(new Date());
};

/**
 * Format date for Indonesian locale display
 * @param {string|Date} date - Date to format
 * @returns {string} Formatted date for Indonesian locale
 */
export const formatDateIndonesian = (date) => {
    const dateObj = typeof date === 'string' ? new Date(date) : date;
    return dateObj.toLocaleDateString("id-ID");
};

/**
 * Format date to short Indonesian format (Kam 11 Sep 2025)
 * @param {string|Date} date - Date to format
 * @returns {string} Short Indonesian date format
 */
export const formatDateShort = (date) => {
    const dateObj = typeof date === 'string' ? new Date(date) : date;
    return dateObj.toLocaleDateString("id-ID", {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};

/**
 * Format date to long Indonesian format (Kamis 11 September 2025)
 * @param {string|Date} date - Date to format
 * @returns {string} Long Indonesian date format
 */
export const formatDateLong = (date) => {
    const dateObj = typeof date === 'string' ? new Date(date) : date;
    return dateObj.toLocaleDateString("id-ID", {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

/**
 * Format date with time (Kamis 11 Sep 2025 06.47)
 * @param {string|Date} date - Date to format
 * @returns {string} Indonesian date with time format
 */
export const formatDateTime = (date) => {
    const dateObj = typeof date === 'string' ? new Date(date) : date;
    const dateStr = dateObj.toLocaleDateString("id-ID", {
        weekday: 'long',
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
    const timeStr = dateObj.toLocaleTimeString("id-ID", {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });
    return `${dateStr} ${timeStr}`;
};

/**
 * Format date with full time (Kamis 11 September 2025 06.47.30)
 * @param {string|Date} date - Date to format
 * @returns {string} Indonesian date with full time format
 */
export const formatDateTimeFull = (date) => {
    const dateObj = typeof date === 'string' ? new Date(date) : date;
    const dateStr = dateObj.toLocaleDateString("id-ID", {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
    const timeStr = dateObj.toLocaleTimeString("id-ID", {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });
    return `${dateStr} ${timeStr}`;
};

/*
===========================================
CONTOH PENGGUNAAN / USAGE EXAMPLES:
===========================================

1. IMPORT:
----------
import { formatDate, getTodayString, formatDateShort } from "@utils/formatDate.js";

2. BASIC USAGE:
---------------
const today = new Date();
const userDate = new Date('2025-09-11');
const apiDate = '2025-09-11T14:30:00';

3. FORMAT EXAMPLES:
-------------------
formatDate(today)              // "2025-01-10"
getTodayString()               // "2025-01-10"
formatDateIndonesian(today)    // "10/1/2025"
formatDateShort(today)         // "Jum, 10 Jan 2025"
formatDateLong(today)          // "Jumat, 10 Januari 2025"
formatDateTime(today)          // "Jumat, 10 Jan 2025 14.30"
formatDateTimeFull(today)      // "Jumat, 10 Januari 2025 14.30.45"

4. REAL WORLD USAGE:
--------------------
// Table filename
const filename = `users-export-${formatDate(new Date())}`;

// Display user-friendly date
const createdAt = formatDateShort(user.created_at);

// Show last login with time
const lastLogin = formatDateTime(user.last_login);

// Full timestamp for logs
const timestamp = formatDateTimeFull(new Date());

5. TABLE COLUMNS:
-----------------
{
    data: "created_at",
    title: "Created",
    render: (data) => formatDateShort(data)
}

6. DEFAULT FILTERS:
-------------------
const DEFAULT_FILTERS = {
    date: getTodayString(),  // Today as default
};

===========================================
*/