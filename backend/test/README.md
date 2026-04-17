# API Tester - React Application

A simple and elegant React web application to test all GET APIs for the Milal Homepage backend.

## Features

- 🧪 **Test All Endpoints** - Click "Test All Endpoints" to test all APIs at once
- 📊 **Individual Testing** - Click on any endpoint button to test it individually
- 📝 **Response Display** - View full response data, messages, and previews
- 📈 **Statistics** - See success/failure counts at a glance
- 🎨 **Responsive Design** - Works on desktop and tablets
- ⚡ **Real-time Updates** - Get instant feedback from API calls

## Tested Endpoints

1. `/api/hero` - Hero section data
2. `/api/sermons` - Sermon records
3. `/api/bulletins` - Bulletin records
4. `/api/announcements` - Announcement records
5. `/api/together` - Together items
6. `/api/news` - News articles
7. `/api/departments` - All departments
8. `/api/nextgen` - NextGen departments
9. `/api/ministry` - Ministry departments

## Setup & Installation

### Prerequisites
- Node.js 16+ installed
- Backend API running on `http://localhost`

### Installation Steps

```bash
# Navigate to the test directory
cd c:\workspace-milal\homepage\backend\test

# Install dependencies
npm install

# Start the development server
npm run dev
```

The application will be available at `http://localhost:3000`

### Build for Production

```bash
npm run build
npm run preview
```

## Project Structure

```
test/
├── src/
│   ├── components/
│   │   ├── ApiTester.jsx         # Main component
│   │   ├── EndpointButton.jsx    # Endpoint button component
│   │   └── ResponseDisplay.jsx   # Response display component
│   ├── styles/
│   │   ├── ApiTester.css         # Main layout styles
│   │   ├── EndpointButton.css    # Button styles
│   │   └── ResponseDisplay.css   # Response display styles
│   ├── App.jsx                    # Root component
│   ├── index.css                  # Global styles
│   └── main.jsx                   # Entry point
├── index.html                     # HTML template
├── vite.config.js                 # Vite configuration
├── package.json                   # Dependencies and scripts
└── README.md                       # This file
```

## How to Use

### Test Individual Endpoints
1. Click on any endpoint button in the left sidebar
2. The API will be called and the response will be displayed on the right
3. View the response data, message, and full JSON response

### Test All Endpoints at Once
1. Click the "Test All Endpoints" button
2. All endpoints will be tested sequentially
3. View statistics showing success/failure count

### Clear Results
1. Click the "Clear All" button to reset all responses and start fresh

## Response Display Features

- **Status Badge** - Shows HTTP status code and success/failure
- **Timestamp** - When the request was made
- **Data Preview** - Shows first 5 items for list endpoints
- **Record Count** - Number of items returned
- **Full Response** - Complete JSON response for debugging

## Styling

The application uses a modern color scheme:
- **Primary**: Blue (`#3b82f6`)
- **Success**: Green (`#10b981`)
- **Error**: Red (`#ef4444`)
- **Text**: Dark gray (`#1f2937`)

## Configuration

To change the API base URL, edit `vite.config.js`:

```javascript
proxy: {
  '/api': {
    target: 'http://your-api-url',
    changeOrigin: true
  }
}
```

## Browser Support

- Chrome/Chromium (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Development

This project uses:
- **React 18** - UI library
- **Vite** - Build tool and dev server
- **Vanilla CSS** - Styling (no dependencies)

## Troubleshooting

### CORS Issues
If you see CORS errors, ensure the backend allows requests from `http://localhost:3000`.

### API Not Responding
- Check that the backend is running on `http://localhost`
- Verify the API endpoints are correct
- Check browser console for error details

### Slow Performance
- Reduce the number of items displayed in preview (edit `ResponseDisplay.jsx`)
- Check network tab for slow API responses

## Future Enhancements

- [ ] POST/PUT/DELETE request testing
- [ ] Request body editor
- [ ] Response history
- [ ] Export/import test scenarios
- [ ] Dark mode
- [ ] Keyboard shortcuts
