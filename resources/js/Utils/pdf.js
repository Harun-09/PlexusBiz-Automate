export const fetchPdfObjectUrl = async (url) => {
    const response = await fetch(url, {
        credentials: 'same-origin',
        cache: 'no-store',
        headers: {
            Accept: 'application/pdf',
        },
    });

    if (!response.ok) {
        throw new Error(`Unable to load PDF (${response.status})`);
    }

    const blob = await response.blob();

    return URL.createObjectURL(blob);
};

export const downloadPdf = async (url, filename) => {
    const objectUrl = await fetchPdfObjectUrl(url);

    try {
        const anchor = document.createElement('a');
        anchor.href = objectUrl;
        anchor.download = filename;
        anchor.rel = 'noopener';
        document.body.appendChild(anchor);
        anchor.click();
        anchor.remove();
    } finally {
        window.setTimeout(() => {
            URL.revokeObjectURL(objectUrl);
        }, 1000);
    }
};
