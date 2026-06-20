import React from 'react';
import { createRoot } from 'react-dom/client';
import LandingPage from './components/LandingPage';

const rootElement = document.getElementById('react-landing-root');
if (rootElement) {
    // Get props safely with a fallback
    let props = {};
    try {
        const rawProps = rootElement.getAttribute('data-props');
        if (rawProps) {
            props = JSON.parse(rawProps);
        }
    } catch (e) {
        console.error("Failed to parse LandingPage props:", e);
    }
    
    const root = createRoot(rootElement);
    root.render(<LandingPage {...props} />);
}
