import '../styles/EndpointButton.css'

function EndpointButton({ endpoint, onClick, isLoading, hasResponse, isSelected, response }) {
  const getStatusClass = () => {
    if (!hasResponse) return ''
    if (response.error) return 'error'
    if (response.ok) return 'success'
    return 'error'
  }

  const getRecordCount = () => {
    if (!response || !response.data) return 0
    const data = response.data.data
    if (Array.isArray(data)) return data.length
    if (typeof data === 'object' && data !== null) return 1
    return 0
  }

  return (
    <button
      className={`endpoint-btn ${getStatusClass()} ${isSelected ? 'selected' : ''}`}
      onClick={onClick}
      disabled={isLoading}
    >
      <div className="btn-header">
        <span className="btn-name">{endpoint.name}</span>
        {hasResponse && (
          <span className={`btn-status ${response.ok ? 'success' : 'error'}`}>
            {response.ok ? '✓' : '✗'} {response.status}
          </span>
        )}
      </div>
      <div className="btn-path">{endpoint.path}</div>
      {hasResponse && response.data && (
        <div className="btn-info">
          {getRecordCount()} record{getRecordCount() !== 1 ? 's' : ''}
        </div>
      )}
    </button>
  )
}

export default EndpointButton
