<template>
  <div class="ball-container">
    <h1 class="title">Hello World</h1>
    <div class="octagon" :style="{ transform: 'rotate(' + angle + 'deg)' }"></div>
    <div 
      v-for="(peg, index) in pegs" 
      :key="index"
      class="peg" 
      :style="{ 
        top: peg.y + 'px', 
        left: peg.x + 'px'
      }"
    ></div>
    <div class="ball" :style="{ top: ballY + 'px', left: ballX + 'px' }"></div>
    <div class="square-ball" :style="{ top: squareBallY + 'px', left: squareBallX + 'px', transform: 'rotate(' + squareBallRotation + 'deg)' }"></div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const angle = ref(0)
const ballX = ref(390)
const ballY = ref(290)  
const velocityX = ref(3)
const velocityY = ref(2)
const squareBallX = ref(350)
const squareBallY = ref(320)
const squareVelocityX = ref(-2.5)
const squareVelocityY = ref(1.8)
const squareBallRotation = ref(0)
const squareRotationSpeed = ref(5)
const ballRadius = 10
const octagonSize = 100 // Half the octagon width/height
const centerX = 400
const centerY = 300
const gravity = 0.15
const bounce = 0.98 // Increased from 0.95 for more bounce
const friction = 0.998 // Reduced friction for more energy
const rotationSpeed = 1
const wallEnergyTransfer = 0.3
const pegRadius = 8

// Generate 3 random pegs inside the octagon
const pegs = ref([
  { x: centerX - 40, y: centerY - 30 },
  { x: centerX + 30, y: centerY - 20 },
  { x: centerX - 10, y: centerY + 35 }
])

let animationId: number

// Define octagon vertices (relative to center)
const getOctagonVertices = (rotation: number) => {
  const vertices = []
  for (let i = 0; i < 8; i++) {
    const angleRad = (i * 45 + rotation) * Math.PI / 180
    const x = centerX + octagonSize * Math.cos(angleRad)
    const y = centerY + octagonSize * Math.sin(angleRad)
    vertices.push({ x, y })
  }
  return vertices
}

// Find closest point on octagon edge and distance
const getClosestEdgePoint = (ballCenterX: number, ballCenterY: number, rotation: number) => {
  const vertices = getOctagonVertices(rotation)
  let minDist = Infinity
  let closestPoint = { x: 0, y: 0 }
  let normalX = 0
  let normalY = 0
  
  for (let i = 0; i < vertices.length; i++) {
    const v1 = vertices[i]
    const v2 = vertices[(i + 1) % vertices.length]
    
    // Calculate closest point on edge
    const edgeX = v2.x - v1.x
    const edgeY = v2.y - v1.y
    const edgeLength = Math.sqrt(edgeX * edgeX + edgeY * edgeY)
    const edgeNormX = edgeX / edgeLength
    const edgeNormY = edgeY / edgeLength
    
    const toBallX = ballCenterX - v1.x
    const toBallY = ballCenterY - v1.y
    const projection = Math.max(0, Math.min(edgeLength, toBallX * edgeNormX + toBallY * edgeNormY))
    
    const pointX = v1.x + projection * edgeNormX
    const pointY = v1.y + projection * edgeNormY
    
    const distX = ballCenterX - pointX
    const distY = ballCenterY - pointY
    const dist = Math.sqrt(distX * distX + distY * distY)
    
    if (dist < minDist) {
      minDist = dist
      closestPoint = { x: pointX, y: pointY }
      // Normal points inward toward ball
      normalX = distX / dist
      normalY = distY / dist
    }
  }
  
  return { distance: minDist, point: closestPoint, normalX, normalY }
}

// Check collision with pegs
const checkPegCollision = (ballCenterX: number, ballCenterY: number, isSquare = false) => {
  for (const peg of pegs.value) {
    const dx = ballCenterX - (peg.x + pegRadius)
    const dy = ballCenterY - (peg.y + pegRadius)
    const distance = Math.sqrt(dx * dx + dy * dy)
    
    if (distance <= ballRadius + pegRadius) {
      const normalX = dx / distance
      const normalY = dy / distance
      
      if (isSquare) {
        // Reflect velocity for square ball
        const dot = squareVelocityX.value * normalX + squareVelocityY.value * normalY
        squareVelocityX.value -= 2 * dot * normalX
        squareVelocityY.value -= 2 * dot * normalY
        
        // Apply bounce
        squareVelocityX.value *= bounce
        squareVelocityY.value *= bounce
        
        // Push square ball out of collision
        const overlap = (ballRadius + pegRadius) - distance
        squareBallX.value += overlap * normalX
        squareBallY.value += overlap * normalY
        
        // Add rotation on collision
        squareRotationSpeed.value += (Math.abs(dot) * 2)
      } else {
        // Reflect velocity for round ball
        const dot = velocityX.value * normalX + velocityY.value * normalY
        velocityX.value -= 2 * dot * normalX
        velocityY.value -= 2 * dot * normalY
        
        // Apply bounce
        velocityX.value *= bounce
        velocityY.value *= bounce
        
        // Push ball out of collision
        const overlap = (ballRadius + pegRadius) - distance
        ballX.value += overlap * normalX
        ballY.value += overlap * normalY
      }
      
      return true
    }
  }
  return false
}

const animate = () => {
  // Update octagon rotation
  angle.value += rotationSpeed
  if (angle.value > 360) {
    angle.value = 0
  }

  // Apply gravity to round ball
  velocityY.value += gravity

  // Apply friction to round ball
  velocityX.value *= friction
  velocityY.value *= friction

  // Update ball position
  ballX.value += velocityX.value
  ballY.value += velocityY.value

  const ballCenterX = ballX.value + ballRadius
  const ballCenterY = ballY.value + ballRadius

  // Apply gravity to square ball
  squareVelocityY.value += gravity

  // Apply friction to square ball
  squareVelocityX.value *= friction
  squareVelocityY.value *= friction

  // Update square ball position
  squareBallX.value += squareVelocityX.value
  squareBallY.value += squareVelocityY.value

  // Update square ball rotation
  squareBallRotation.value += squareRotationSpeed.value
  squareRotationSpeed.value *= 0.99 // Slow down rotation over time

  const squareBallCenterX = squareBallX.value + ballRadius
  const squareBallCenterY = squareBallY.value + ballRadius

  // Check collision between the two balls
  const dx = squareBallCenterX - ballCenterX
  const dy = squareBallCenterY - ballCenterY
  const distance = Math.sqrt(dx * dx + dy * dy)
  const minDistance = ballRadius * 2 // Both balls have same radius
  
  if (distance < minDistance && distance > 0) {
    // Collision detected between balls
    const normalX = dx / distance
    const normalY = dy / distance
    
    // Calculate relative velocity
    const relativeVelX = squareVelocityX.value - velocityX.value
    const relativeVelY = squareVelocityY.value - velocityY.value
    const relativeVel = relativeVelX * normalX + relativeVelY * normalY
    
    // Only resolve if balls are moving toward each other
    if (relativeVel < 0) {
      // Reflect velocities for elastic collision
      const impulse = 2 * relativeVel / 2 // Equal mass assumption
      
      velocityX.value += impulse * normalX
      velocityY.value += impulse * normalY
      squareVelocityX.value -= impulse * normalX
      squareVelocityY.value -= impulse * normalY
      
      // Apply bounce factor
      velocityX.value *= bounce
      velocityY.value *= bounce
      squareVelocityX.value *= bounce
      squareVelocityY.value *= bounce
      
      // Add rotation to square ball on collision
      squareRotationSpeed.value += Math.abs(relativeVel) * 2
      
      // Separate the balls to prevent sticking
      const overlap = minDistance - distance
      const separationX = (overlap / 2) * normalX
      const separationY = (overlap / 2) * normalY
      
      ballX.value -= separationX
      ballY.value -= separationY
      squareBallX.value += separationX
      squareBallY.value += separationY
    }
  }

  // Check collision with pegs first
  checkPegCollision(ballCenterX, ballCenterY, false)
  checkPegCollision(squareBallCenterX, squareBallCenterY, true)

  // Check collision with octagon edges for round ball
  const collision = getClosestEdgePoint(ballCenterX, ballCenterY, angle.value)
  
  if (collision.distance <= ballRadius) {
    // Calculate tangent vector for wall velocity
    const tangentX = -collision.normalY
    const tangentY = collision.normalX

    // Wall velocity from rotation
    const angularVelocity = rotationSpeed * Math.PI / 180
    const wallVelocityX = tangentX * octagonSize * angularVelocity
    const wallVelocityY = tangentY * octagonSize * angularVelocity

    // Reflect velocity
    const dot = velocityX.value * collision.normalX + velocityY.value * collision.normalY
    velocityX.value -= 2 * dot * collision.normalX
    velocityY.value -= 2 * dot * collision.normalY
    
    // Add energy from rotating wall
    velocityX.value += wallVelocityX * wallEnergyTransfer
    velocityY.value += wallVelocityY * wallEnergyTransfer

    // Apply bounce
    velocityX.value *= bounce
    velocityY.value *= bounce

    // Ensure minimum velocity
    const speed = Math.sqrt(velocityX.value * velocityX.value + velocityY.value * velocityY.value)
    if (speed < 1.5) {
      const minSpeed = 2
      velocityX.value = (velocityX.value / speed) * minSpeed
      velocityY.value = (velocityY.value / speed) * minSpeed
    }

    // Push ball out of collision
    const overlap = ballRadius - collision.distance
    ballX.value += overlap * collision.normalX
    ballY.value += overlap * collision.normalY
  }

  // Check collision with octagon edges for square ball
  const squareCollision = getClosestEdgePoint(squareBallCenterX, squareBallCenterY, angle.value)
  
  if (squareCollision.distance <= ballRadius) {
    // Calculate tangent vector for wall velocity
    const tangentX = -squareCollision.normalY
    const tangentY = squareCollision.normalX

    // Wall velocity from rotation
    const angularVelocity = rotationSpeed * Math.PI / 180
    const wallVelocityX = tangentX * octagonSize * angularVelocity
    const wallVelocityY = tangentY * octagonSize * angularVelocity

    // Reflect velocity
    const dot = squareVelocityX.value * squareCollision.normalX + squareVelocityY.value * squareCollision.normalY
    squareVelocityX.value -= 2 * dot * squareCollision.normalX
    squareVelocityY.value -= 2 * dot * squareCollision.normalY
    
    // Add energy from rotating wall
    squareVelocityX.value += wallVelocityX * wallEnergyTransfer
    squareVelocityY.value += wallVelocityY * wallEnergyTransfer

    // Apply bounce
    squareVelocityX.value *= bounce
    squareVelocityY.value *= bounce

    // Add rotation on collision
    squareRotationSpeed.value += (Math.abs(dot) * 3)

    // Ensure minimum velocity
    const speed = Math.sqrt(squareVelocityX.value * squareVelocityX.value + squareVelocityY.value * squareVelocityY.value)
    if (speed < 1.5) {
      const minSpeed = 2
      squareVelocityX.value = (squareVelocityX.value / speed) * minSpeed
      squareVelocityY.value = (squareVelocityY.value / speed) * minSpeed
    }

    // Push square ball out of collision
    const overlap = ballRadius - squareCollision.distance
    squareBallX.value += overlap * squareCollision.normalX
    squareBallY.value += overlap * squareCollision.normalY
  }

  animationId = requestAnimationFrame(animate)
}

onMounted(() => {
  animate()
})

onUnmounted(() => {
  if (animationId) {
    cancelAnimationFrame(animationId)
  }
})
</script>

<style scoped>
.title {
  position: absolute;
  top: 2rem;
  left: 50%;
  transform: translateX(-50%);
  font-size: 3rem;
  font-weight: bold;
  color: white;
  z-index: 10;
}

.ball-container {
  position: relative;
  width: 800px;
  height: 600px;
  margin: 0 auto;
  background: linear-gradient(135deg, #1e3a8a, #7c3aed);
}

.octagon {
  position: absolute;
  top: 50%;
  left: 50%;
  transform-origin: center;
  width: 200px;
  height: 200px;
  background-color: #333;
  clip-path: polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%);
  margin: -100px 0 0 -100px;
  animation: rotate 4s linear infinite;
}

.ball {
  position: absolute;
  border-radius: 50%;
  background-color: #fff;
  width: 20px;
  height: 20px;
  box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

.peg {
  position: absolute;
  border-radius: 50%;
  background-color: #ff6b6b;
  width: 16px;
  height: 16px;
  box-shadow: 0 0 8px rgba(255, 107, 107, 0.7);
}

.square-ball {
  position: absolute;
  background-color: #fbbf24;
  width: 20px;
  height: 20px;
  box-shadow: 0 0 10px rgba(251, 191, 36, 0.5);
}

@keyframes rotate {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>