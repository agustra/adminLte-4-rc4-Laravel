export default function Rupiah(angka) {
    if (typeof angka === "string") {
        angka = angka.replace(/[^\d.-]/g, "");
    }

    let parsed = parseFloat(angka);
    
    if (isNaN(parsed)) return "Rp 0";

    // Format without decimals to match form input
    const integerPart = Math.floor(parsed).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    
    return `Rp ${integerPart}`;
}
