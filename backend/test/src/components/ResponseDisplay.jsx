import '../styles/ResponseDisplay.css'

function ResponseDisplay({ endpoint, response }) {
  if (!response) {
    return <div className="response-display empty">No response yet</div>
  }

  const renderDataPreview = () => {
    if (!response.data) return null
    
    const data = response.data.data
    if (!data) return null

    if (Array.isArray(data)) {
      return (
        <div className="data-preview">
          <h4>Data Preview ({data.length} items)</h4>
          <div className="items-list">
            {data.slice(0, 5).map((item, idx) => (
              <div key={idx} className="item-preview">
                <pre>{JSON.stringify(item, null, 2)}</pre>
              </div>
            ))}
            {data.length > 5 && (
              <p className="more-items">... and {data.length - 5} more items</p>
            )}
          </div>
        </div>
      )
    } else if (typeof data === 'object') {
      return (
        <div className="data-preview">
          <h4>Data Preview</h4>
          <pre>{JSON.stringify(data, null, 2)}</pre>
        </div>
      )
    }
  }

  return (
    <div className="response-display">
      <div className="response-header">
        <h3>{endpoint}</h3>
        <span className={`response-status ${response.ok ? 'success' : 'error'}`}>
          {response.ok ? '✓ Success' : '✗ Error'} ({response.status})
        </span>
        <span className="response-time">{response.timestamp}</span>
      </div>

      {response.error && (
        <div className="error-box">
          <p><strong>Error:</strong> {response.error}</p>
        </div>
      )}

      {response.data && (
        <div className="response-details">
          <div className="response-meta">
            <p><strong>Message:</strong> {response.data.message || 'N/A'}</p>
            <p><strong>Status:</strong> {response.data.success ? 'Success' : 'Failed'}</p>
          </div>

          {renderDataPreview()}

          <div className="raw-response">
            <h4>Full Response</h4>
            <pre>{JSON.stringify(response.data, null, 2)}</pre>
          </div>
        </div>
      )}
    </div>
  )
}

export default ResponseDisplay
