import React from 'react'

const Card = (props) => {
  return (
    <div className='cd-card-container'>
         <h2 className="cd-card-number">{props.index}</h2>
         <h2 className="cd-card-title">{props.title}</h2>
         <p className='cd-card-description'>{props.description}</p>

    </div>
  )
}

export default Card
