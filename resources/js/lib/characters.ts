// Character types and utilities for the Star Wars survey
// All character data is now fetched from the database

export interface CharacterOption {
    value: string;
    label: string;
    description: string;
}

// Function to fetch character options from the API
// This is kept for any components that may need to fetch characters dynamically
export const fetchCharacterOptions = async (): Promise<CharacterOption[]> => {
    try {
        const response = await fetch('/api/characters');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        return data.map((char: any) => ({
            value: char.value,
            label: char.label,
            description: char.description
        }));
    } catch (error) {
        console.error('Error fetching character options:', error);
        return [];
    }
};
