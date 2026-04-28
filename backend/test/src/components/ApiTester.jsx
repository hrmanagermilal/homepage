import { useState } from 'react'
import EndpointButton from './EndpointButton'
import ResponseDisplay from './ResponseDisplay'
import '../styles/ApiTester.css'

const API_ENDPOINTS = [
  { name: 'Hero', path: '/api/hero' },
  { name: 'Hero Links', path: '/api/hero-links' },
  { name: 'Landing Titles', path: '/api/landing-titles' },
  { name: 'Sermons', path: '/api/sermons' },
  { name: 'Bulletins', path: '/api/bulletins' },
  { name: 'Announcements', path: '/api/announcements' },
  { name: 'Together Items', path: '/api/together' },
  { name: 'News', path: '/api/news' },
  { name: 'Members', path: '/api/members' },
  { name: 'Departments', path: '/api/departments' },
  { name: 'NextGen (Departments)', path: '/api/nextgen' },
  { name: 'Ministry (Departments)', path: '/api/ministry' },
]

function ApiTester() {
  const [responses, setResponses] = useState({})
  const [loading, setLoading] = useState(false)
  const [selectedEndpoint, setSelectedEndpoint] = useState(null)

  const testEndpoint = async (endpoint) => {
    setLoading(true)
    setSelectedEndpoint(endpoint.path)
    
    try {
      const response = await fetch(endpoint.path)
      const data = await response.json()
      
      setResponses(prev => ({
        ...prev,
        [endpoint.path]: {
          status: response.status,
          ok: response.ok,
          data: data,
          timestamp: new Date().toLocaleTimeString(),
          error: null
        }
      }))
    } catch (error) {
      setResponses(prev => ({
        ...prev,
        [endpoint.path]: {
          status: 'ERROR',
          ok: false,
          data: null,
          timestamp: new Date().toLocaleTimeString(),
          error: error.message
        }
      }))
    } finally {
      setLoading(false)
    }
  }

  const testAll = async () => {
    setLoading(true)
    for (const endpoint of API_ENDPOINTS) {
      try {
        const response = await fetch(endpoint.path)
        const data = await response.json()
        
        setResponses(prev => ({
          ...prev,
          [endpoint.path]: {
            status: response.status,
            ok: response.ok,
            data: data,
            timestamp: new Date().toLocaleTimeString(),
            error: null
          }
        }))
      } catch (error) {
        setResponses(prev => ({
          ...prev,
          [endpoint.path]: {
            status: 'ERROR',
            ok: false,
            data: null,
            timestamp: new Date().toLocaleTimeString(),
            error: error.message
          }
        }))
      }
      // Small delay between requests
      await new Promise(resolve => setTimeout(resolve, 100))
    }
    setLoading(false)
  }

  const clearAll = () => {
    setResponses({})
    setSelectedEndpoint(null)
  }

  return (
    <div className="api-tester">
      <header className="tester-header">
        <h1>🔧 API Tester</h1>
        <p>Test all GET endpoints for Milal Homepage API</p>
      </header>

      <div className="controls">
        <button 
          className="btn btn-primary" 
          onClick={testAll}
          disabled={loading}
        >
          {loading ? 'Testing...' : 'Test All Endpoints'}
        </button>
        <button 
          className="btn btn-secondary"
          onClick={clearAll}
          disabled={loading}
        >
          Clear All
        </button>
      </div>

      <div className="tester-container">
        <div className="endpoints-panel">
          <h2>Endpoints</h2>
          <div className="endpoint-buttons">
            {API_ENDPOINTS.map((endpoint) => (
              <EndpointButton
                key={endpoint.path}
                endpoint={endpoint}
                onClick={() => testEndpoint(endpoint)}
                isLoading={loading}
                hasResponse={endpoint.path in responses}
                isSelected={selectedEndpoint === endpoint.path}
                response={responses[endpoint.path]}
              />
            ))}
          </div>
        </div>

        <div className="response-panel">
          {selectedEndpoint ? (
            <ResponseDisplay 
              endpoint={selectedEndpoint}
              response={responses[selectedEndpoint]}
            />
          ) : (
            <div className="empty-state">
              <p>👆 Select an endpoint to view response</p>
            </div>
          )}
        </div>
      </div>

      <div className="stats">
        <p>
          Total Endpoints: <strong>{API_ENDPOINTS.length}</strong> | 
          Tested: <strong>{Object.keys(responses).length}</strong> |
          Success: <strong>{Object.values(responses).filter(r => r.ok).length}</strong> |
          Failed: <strong>{Object.values(responses).filter(r => !r.ok).length}</strong>
        </p>
      </div>
    </div>
  )
}

export default ApiTester
